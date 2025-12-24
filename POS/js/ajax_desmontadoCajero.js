/* global alertify, parseFloat */

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: JOSE FERNANDEZ//////////////////////
////////DESCRIPCION		: AJAX DE PANTALLA DESMONTADO CAJERO//
////////TABLAS			: ARQUEO_CAJA,BILLETE_ESTACION,///////
//////////////////////////CONTROL_ESTACION,ESTACION///////////
//////////////////////////BILLETE_DENOMINACION////////////////
////////FECHA CREACION	: 20/12/2013//////////////////////////
//////////////////////////////////////////////////////////////

var bandera = 0;
var arrayBilletes2 = new Array();
var array = 50;
var ocultos2 = new Array(array);
var ocultos = new Array(array);
var ocultos3 = new Array(array); //id de usuario
var resultado = new Array(array);
var denominaciones = new Array(array);
var multiplicaciones = new Array(array);
var cantidades = new Array(array);
var subtotales = new Array(array); //array q contiene los datos de la columna de totales
var id_botones = new Array();
var id_botonesRetiro = new Array();
var id_botonesArqueoRetiro = new Array();
var id_botonesCuadreTarjetas = new Array();
var id_botonesfp = new Array(array);
var id_botones_posicionArray = 0;
var valor_total_efectivo = 0;
var banderaDesmontado = 0; //bandera para saber si es Retiro de dinero, Arqueo ó Desasignar Cajero
var totalEfectivo = 0;
var arrayArqueo = new Array(); //almacena descripcion de las formas de pago
var array_IDFormasPago = new Array(); //almacena los ids de las formas de pago
var array_montoActual = new Array(); //almacena los valores en arqueo retiro de dinero
var array_transaccionesIngresadas = new Array(); //almacena los valores de transacciones ingresadas en arqueo retiro de dinero
var array_diferencia = new Array(); //almacena los valores de diferencia en arqueo retiro de dinero
var array_posCalculado = new Array(); //almacena los valores de posCalculado en arqueo retiro de dinero
var array_retirado = new Array(); //almacena los valores totales de retiro de cada forma de pago en arqueo retiro de dinero
var array_transacciones = new Array(); //almacena los valores de transacciones en arqueo retiro de dinero
var arrayArqueoCuadreTarjetas = new Array();
var totalEstacion = 0;
var transaccionesIngresadas = 0;
var estadoSwitch = 0;
var TotalEgresos = 0;
var TotalIngresos = 0;

//Para Transferencia de venta de heladería
var debeRealizarTransferencia = false;
var tipoLocalTransferenciaVenta = "NORMAL";
var valorTotalTransferenciaVenta = 0;
var valorTotalTransferenciaVentaHeladeria = "";

var DATOS_TRANSFERENCIA = "0";
var TRANSFERENCIA_TIPO = 0;
var estaciones = '';
var nroestaciones = 0;
var IDControlEstacionAutomatico = '';

var ES_ULTIMA_CAJA = 0;
/*Inicio ventana modal de inicio*/
$(document).ready(function() {
    var send;
    fn_desasignarEnEstacion();
    $("#modal_inicio").hide();
    $("#modal_formaPago").hide();
    $("#dialog2").hide();
    $("#dialogEfectivo").hide();
    $("#div_billetes").shortscroll();
    $("#div_billetesEfectivo").shortscroll();
    $("#div_formaPago").shortscroll();
    $("#div_formaPagoEfectivo").shortscroll();
    $("#credencialesAdmin").hide();

    send = { consultausuarioenEstacion: 1 };
    send.accion = 1;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            $("#hid_usuario").val(datos.usr_id);
            $("#hid_usuarioDescripcion").val(datos.usr_usuario);
            $("#hid_restaurante").val(datos.rst_id);
            $("#cedulaCajero").val(datos.cedula);
            $("#fecha").val(datos.fecha);
        }
    });

    $("#btn_corteCaja").click(function() {

        id_botones.length = 0; // vaciamos el arreglo
        banderaDesmontado = 2; //Desasignar Cajero
        //Limpiamos cajas de texto de los valores de formas de pago
        $("#totalRetirado").val("");
        $("#transacciones").val("");
        $("#posCalculadoValor").val("");
        $("#diferencia").text("");
        $("#hid_formaPago").val("");
        $("#hid_usuario_efectivo").val("");
        banderaArqueoTarjeta = 0;
        $("#modal_inicio").dialog("close");
        //validacion
        let unificacion=$("#unificacion_transferencia_de_venta").val();
        if(unificacion!=1){ 
            if (debeRealizarTransferencia && valorTotalTransferenciaVenta > 0) {
                if (tipoLocalTransferenciaVenta === "ORIGEN") {
                    alertify.alert("Antes de desasignar el cajero/a, debe realizar la transferencia de venta : " + valorTotalTransferenciaVenta);
                    $("#alertify-ok").click(function() {});
                }

                if (tipoLocalTransferenciaVenta === "DESTINO") {
                    alertify.alert("Antes de desasignar el cajero/a, debe recibir la transferencia de venta: " + valorTotalTransferenciaVentaHeladeria);
                    $("#alertify-ok").click(function() {
                        location.reload();
                    });

                }

                return false;
            }
        }
        send = { existeCuentaAbiertaMesa: 1 };
        send.accion = 2;
        $.ajax({
            async: false,
            type: "GET",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_desmontadoCajero.php",
            data: send,
            success: function(datos) {
                if (datos.str > 0) {
                    if (datos.cuentaAbiertaMesa == 0) {
                        send = { existeCuentaAbierta: 1 };
                        send.accion = 1;
                        $.ajax({
                            async: false,
                            type: "GET",
                            dataType: "json",
                            contentType: "application/x-www-form-urlencoded",
                            url: "config_desmontadoCajero.php",
                            data: send,
                            success: function(datos) {
                                if (datos.str > 0) {
                                    if (datos.cuentaAbierta === 1) {
                                        nombreUsr = $("#hid_usuarioDescripcion").val();
                                        alertify.alert("No puede desasignar el cajero porque el usuario (" + nombreUsr + ") tiene ordenes abiertas");
                                        $("#alertify-ok").click(function() {
                                            bandera = $("#bandera").val();
                                            if (bandera === "Inicio") {
                                                window.location.replace("../index.php");
                                            } else {
                                                window.location.replace("corteCaja.php");
                                            }
                                        });
                                    } else {
                                        var mensaje = getCuentasAbiertaTodasEstaciones();
                                        if(mensaje != ''){
                                            if (mensaje == "Actualmente tienes pedidos de domicilio pendiente") {
                                                alertify.alert(mensaje);
                                                $("#alertify-ok").click(function() {
                                                    window.location.replace("../ordenPedido/userMesas.php?PedidosPendiente=1");
                                                });
                                            }else if (mensaje == "Actualmente tienes pedidos de kiosko pendiente") {
                                                alertify.alert(mensaje);
                                                $("#alertify-ok").click(function() {

                                                    window.location.replace("../ordenPedido/userMesas.php?pedidoRapidoPendiente=1");
                                                    
                                                });
                                            }else if (mensaje == "Actualmente tienes pedidos de PickUp pendiente") {
                                                alertify.alert(mensaje);
                                                $("#alertify-ok").click(function() {
                                                    window.location.replace("../ordenPedido/userMesas.php?pedidoRapidoPendiente=1");
                                                });
                                            } else {
                                                
                                                alertify.alert(mensaje);
                                                $("#alertify-ok").click(function() {
                                                    window.location.replace("ccorteCaja.php");
                                                });
                                            }

                                    }else{

                                        send = { retirofondo: 1 };
                                        send.accion = 1;
                                        $.ajax({
                                            async: false,
                                            type: "GET",
                                            dataType: "json",
                                            contentType: "application/x-www-form-urlencoded",
                                                        url: "config_desmontadoCajero.php",
                                            data: send,
                                            success: function(datos) {
                                                if (datos.str > 0) {
                                                    if (datos.retirofondo === 1) {
                                                        fn_retiroCashless();
                                                        fn_consultaFormaPago();
                                                    } else if (datos.retirofondo === 2) {
                                                        alertify.alert("No puede desasignar el cajero porque tiene pedidos DOMICILIO sin facturar");
                                                    } else {
                                                        alertify.set({ labels: { ok: "SI", cancel: "NO" } });
                                                        alertify.confirm("No puede desasignar el cajero porque el Administrador no ha retirado el fondo. Desea retirarlo?");
                                                        $("#alertify-ok").click(function() {
                                                            var banderaRetiroFondo = "volverDesasignar";
                                                            window.location.replace("../retiro_fondo/retiro_fondo.php?bandera=" + banderaRetiroFondo);
                                                        });
                                                    }
                                                }
                                            }
                                        });


                                        }
                                        

                                    }
                                }
                            }
                        });
                    } else {
                        var mensaje = getCuentasAbiertaTodasEstaciones();
                        if(mensaje != ''){
                            
                            if (mensaje == "Actualmente tienes pedidos de domicilio pendiente") {
                                alertify.alert(mensaje);
                                $("#alertify-ok").click(function() {
                                    window.location.replace("../ordenPedido/userMesas.php?PedidosPendiente=1");
                                });
                            }else if (mensaje == "Actualmente tienes pedidos de kiosko pendiente") {
                                alertify.alert(mensaje);
                                $("#alertify-ok").click(function() {
                                    window.location.replace("../ordenPedido/userMesas.php?pedidoRapidoPendiente=1");
                                });
                            }else if (mensaje == "Actualmente tienes pedidos de PickUp pendiente") {
                                alertify.alert(mensaje);
                                $("#alertify-ok").click(function() {
                                    window.location.replace("../ordenPedido/userMesas.php?pedidoRapidoPendiente=1");
                                });
                            } else {
                                
                                alertify.alert(mensaje);
                                $("#alertify-ok").click(function() {
                                    window.location.replace("corteCaja.php.php");
                                });
                            }

                        }else{
                            nombreUsr = $("#hid_usuarioDescripcion").val();
                            alertify.alert("No puede desasignar el cajero porque el usuario (" + nombreUsr + ") tiene cuentas abiertas");
                            $("#alertify-ok").click(function() {
                                bandera = $("#bandera").val();
                                if (bandera == "Inicio") {
                                    window.location.replace("../index.php");
                                } else {
                                window.location.replace("corteCaja.php");
                                }
                            });
                        }
                       
                    }
                }
            }
        });
    });

    //});

    $("#btn_cancelarInicio").click(function() {
        bandera = $("#bandera").val();
        if (bandera == "Inicio") {
            window.location.replace("../index.php");
        } else {
            window.location.replace("corteCaja.php");
        }
    });

    var getCuentasAbiertaTodasEstaciones = function(){
        var send;
        var mensaje = '';
        send = { verificaCuentasAbiertaTodasEstaciones: 1 };
        $.ajax({
            async: false,
            type: "GET",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_desmontadoCajero.php",
            data: send,
            success: function(datos) {
                if (datos.str > 0) {
                    if((datos.cuentas_abiertas == 1 || datos.cuentas_abiertas_mesa == 1) && datos.es_ultima_caja == 1){
                        mensaje = datos.mensaje;
                        ES_ULTIMA_CAJA = datos.es_ultima_caja;
                    }
                }
            }
        });
        return mensaje;
    }

    /* Para decidir si se muestra o no se muestra el botón de Transferencia de venta */
    var crearBotonTransferencia = function() {
      var $contBtnTransferencia = $("#contBtnTransferencia");
      var $btn = $("<div class='form-group col-md-10 col-md-offset-1'><button class='btn btn-lg btn-primary btn-block iniciarsesion' style='height:80px' id='btn_transferenciaVenta' >TRANSFERENCIA VENTA</button></div>");
      var $ModalTransferenciaVenta = $("#ModalTransferenciaVenta");
      var $totalTransferenciaVenta = $("#totalTransferenciaVenta");
      var $btnCancelarTransferenciaVenta = $("#btnCancelarTransferenciaVenta");
      var $btnAceptarTransferenciaVenta = $("#btnAceptarTransferenciaVenta");
      var $mensajeModalTransferenciaVenta = $("#mensajeModalTransferenciaVenta");
      var fun = function() {
        fn_cargando(1);
        var send = { accion: "consultaCajeroAutomaticoActivo" };
        $.ajax({
          async: false,
          type: "POST",
          dataType: "json",
          contentType: "application/x-www-form-urlencoded",
          url: "config_clienteEgreso.php",
          data: send,
          success: function(datos) {
            if (!datos.respuesta) {
              fn_cargando(0);
              alertify.alert("Existen problemas al realizar está operación.");
              return false;
            }
            if (datos.respuesta[1] == 1) {
              //pantalla donde se elige la estacion donde va a realizar la transferencia
              estaciones = datos.respuesta[3];
              nroestaciones = datos.respuesta[0];
              if (nroestaciones == 1){
                var objEstaciones = JSON.parse(estaciones);
                IDControlEstacionAutomatico = objEstaciones[0]['IDControlEstacion'];
                var send = { consultaValorTransferencia: 1 };
                $.ajax({
                  async: false,
                  type: "GET",
                  dataType: "json",
                  contentType: "application/x-www-form-urlencoded",
                  url: "config_desmontadoCajero.php",
                  data: send,
                  success: function(datos) {
                    valorTotalTransferenciaVenta = datos.totalTransferencia;
                    DATOS_TRANSFERENCIA = datos.json_datosTransferencia;
                    TRANSFERENCIA_TIPO = datos.tipo_transferencia;
                    if (datos.totalTransferencia > 0) {
                      $mensajeModalTransferenciaVenta.html("El valor para transferencia de venta es de:");
                      $btnCancelarTransferenciaVenta.html("Cancelar");
                      $btnAceptarTransferenciaVenta.show();
                    } else {
                      $btnAceptarTransferenciaVenta.hide();
                      $btnCancelarTransferenciaVenta.css("margin-left", "");
                      $btnCancelarTransferenciaVenta.show();
                      $btnCancelarTransferenciaVenta.html("OK");
                      $mensajeModalTransferenciaVenta.html("No hay valores pendientes de transferencia");
                    }
                  $totalTransferenciaVenta.html(valorTotalTransferenciaVenta);
                  $ModalTransferenciaVenta.modal("show");
                  }
                });
              }
              else{
                $("#mensajeModalCajerosTransferenciaVenta").html(datos.respuesta[2]+':');
                var objEstaciones = JSON.parse(estaciones);
                console.log(objEstaciones);
                var htmlCajeros = '';
                $.each(objEstaciones, function(key, value) {
                  htmlCajeros = htmlCajeros + '<br><button type="button" class="btn btn-primary" style="height:50px;width:250px;" onclick="seleccionarCajero(\''+value['IDControlEstacion']+'\');">'+value['usr_usuario']+'</button><br>';   
                });
                $("#listadoModalCajerosTransferenciaVenta").html(htmlCajeros);
                $("#ModalCajerosTransferenciaVenta").modal("show");
              } 
            } else {
                fn_cargando(0);
                alertify.alert(datos.respuesta[2]);
                return false;
            }
          },
          error: function() { // Cuando el WS no esta en linea 
            fn_cargando(0);
            alertify.alert("Existen problemas al realizar está operación.");
            return false;
          },
          complete: function() {
            fn_cargando(0);
          }
        });
      };
      $btn.on("click", fun);
      $contBtnTransferencia.append($btn);
      return true;
    };

    var confirmarRealizaTransferencia = function(callbackfunction) {
        var send = { realizaTransferencia: 1 };
        $.ajax({
            async: false,
            type: "GET",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_desmontadoCajero.php",
            data: send,
            success: function(datos) {
                tipoLocalTransferenciaVenta = datos.Valida;
                callbackfunction(datos);
            }
        });
    };
    var calcularValorTransferencia = function(callbackfunction) {
        var send = { consultaValorTransferencia: 1 };
        $.ajax({
            async: false,
            type: "GET",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_desmontadoCajero.php",
            data: send,
            success: function(datos) {
                callbackfunction(datos);
            }
        });
    };
    var actualizaBanderasTransferencia = function(datos) {
        if (datos.totalTransferencia > 0 && tipoLocalTransferenciaVenta !== "NORMAL") {
            debeRealizarTransferencia = true;
            valorTotalTransferenciaVenta = datos.totalTransferencia;
        } else {
            debeRealizarTransferencia = false;
            valorTotalTransferenciaVenta = 0;
        }
    };
    var colocarBotonTransferencia = function(datos) {
        if (datos.Valida === "ORIGEN") {
            crearBotonTransferencia();
        }
        if (datos.Valida === "DESTINO") {
            var send;
            send = { accion: "validaTransferenciaHeladeria" };
            $.ajax({
                async: false,
                type: "POST",
                dataType: "json",
                contentType: "application/x-www-form-urlencoded",
                url: "config_clienteEgreso.php",
                data: send,
                success: function(datosDest) {
                    if (datosDest.respuesta.length > 0) {
                        valorTotalTransferenciaVenta = 1;
                        debeRealizarTransferencia = true;
                        for (var i = 0; i <= datosDest.respuesta.length; i++) {
                            valorTotalTransferenciaVentaHeladeria +=
                                "<br>" +
                                datosDest.respuesta[i]["nombre"] +
                                " : " +
                                datosDest.respuesta[i]["transferencia"] +
                                "<br>";
                        }
                        actualizaBanderasTransferencia(valorTotalTransferenciaVenta);
                    } else {
                        debeRealizarTransferencia = false;
                        valorTotalTransferenciaVenta = 0;
                        actualizaBanderasTransferencia(valorTotalTransferenciaVenta);
                    }
                }
            });
        }
    };

    let $btnAceptarTransferenciaVenta = $("#btnAceptarTransferenciaVenta");

    $btnAceptarTransferenciaVenta.one("click", function() {
        $("#btnAceptarTransferenciaVenta").prop("disabled", true);
        $("#btnAceptarTransferenciaVenta").hide();
        $("#btnCancelarTransferenciaVenta").hide();

        fn_cargando(1);
        let $ModalTransferenciaVenta = $("#ModalTransferenciaVenta");
        let valorTransferencia = valorTotalTransferenciaVenta;
        let periodoActual = $("#IDPeriodo").val();
        let transferencia_datos = DATOS_TRANSFERENCIA;
        let tipo_trasferencia_venta = TRANSFERENCIA_TIPO;

        let sendws = {
            accion: "inyectaIngresoDestino",
            valor: valorTransferencia,
            tipo_transferencia: tipo_trasferencia_venta,
            datos: transferencia_datos,
            periodo: periodoActual,
            IDControlEstacionAutomatico: IDControlEstacionAutomatico
        };
        
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_clienteEgreso.php",
            data: sendws,
            success: function(datos) {
                if (datos) {

                        let send = { generaEgresoOrigen: 1 };
                        $.ajax({
                            async: false,
                            type: "GET",
                            dataType: "json",
                            contentType: "application/x-www-form-urlencoded",
                            url: "config_desmontadoCajero.php",
                            data: send,
                            success: function(datos) {
                                if (datos === true) {

                                    let apiImpresion = getConfiguracionesApiImpresion();    
                                    if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
                                        fn_cargando(1);
                                        let result = new apiServicioImpresion('transferencia');
                                        let imprime = result['imprime'];
                                        let mensaje = result['mensaje'];
                                        console.log('imprime: ', result);
                                        if (!mensaje) {
                                            alertify.success('Imprimiendo transferencia de venta...');
                                            fn_cargando(0);
                                        } else {
                                            alertify.success(mensaje);
                                            fn_cargando(0);
                                        }
                                    }

                                    alertify.set({ labels: { ok: "OK" } });
                                    alertify.alert("Egreso generado correctamente");

                                    $("#alertify-ok").click(function() {
                                        $ModalTransferenciaVenta.modal("hide");
                                    });
                                } else {
                                    $("#btnAceptarTransferenciaVenta").prop('disabled', false);
                                    $("#btnAceptarTransferenciaVenta").show();
                                    $("#btnCancelarTransferenciaVenta").show();
                                    alertify.alert("Error al generar el egreso");
                                }
                            }
                        });
                    
                    calcularValorTransferencia(actualizaBanderasTransferencia);
                } else {
                    $("#btnAceptarTransferenciaVenta").prop('disabled', false);
                    $("#btnAceptarTransferenciaVenta").show();
                    $("#btnCancelarTransferenciaVenta").show();

                    alertify.alert("Transferencia No Realizada. Existen problemas al realizar esta operación.");
                    console.log('Error al consultar el WS.');
                }
            }
        });

        fn_cargando(0);
        calcularValorTransferencia(actualizaBanderasTransferencia);
    });

    confirmarRealizaTransferencia(colocarBotonTransferencia);
    calcularValorTransferencia(actualizaBanderasTransferencia);
});

function seleccionarCajero(paramIdControlEstacion){
  console.log(paramIdControlEstacion);
  IDControlEstacionAutomatico = paramIdControlEstacion; 
  $("#ModalCajerosTransferenciaVenta").modal("hide");        
  var send = { consultaValorTransferencia: 1 };
  $.ajax({
    async: false,
    type: "GET",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "config_desmontadoCajero.php",
    data: send,
    success: function(datos) {
      valorTotalTransferenciaVenta = datos.totalTransferencia;
      DATOS_TRANSFERENCIA = datos.json_datosTransferencia;
      TRANSFERENCIA_TIPO = datos.tipo_transferencia;
      if (datos.totalTransferencia > 0) {
        $("#mensajeModalTransferenciaVenta").html("El valor para transferencia de venta es de:");
        $("#btnCancelarTransferenciaVenta").html("Cancelar");
        $("#btnAceptarTransferenciaVenta").show();
      } else {
        $("#btnAceptarTransferenciaVenta").hide();
        $("#btnCancelarTransferenciaVenta").css("margin-left", "");
        $("#btnCancelarTransferenciaVenta").show();
        $("#btnCancelarTransferenciaVenta").html("OK");
        $("#mensajeModalTransferenciaVenta").html("No hay valores pendientes de transferencia");
      }
      $("#totalTransferenciaVenta").html(valorTotalTransferenciaVenta);
      $("#ModalTransferenciaVenta").modal("show");
    }
  });
}

function fn_consultaFormaPago() {
    send = { consultaformaPago: 1 };
    send.accion = 1;
    send.banderaDesmontado = banderaDesmontado;
    idformaPago = 0;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            var html = "<thead><tr class='active'><th width='130px' style='text-align:center'>Formas de Pago</th><th width='130px' style='text-align:center'>Egresos</th><th width='130px' style='text-align:center'>Ingresos</th><th width='130px' style='text-align:center'>Retirado</th><th width='130px' style='text-align:center'>Transacciones</th><th style='text-align:center' width='130px'>Valor Declarado</th><th style='text-align:center' width='130px'>POS Calculado</th><th style='text-align:center' width='130px'>Diferencia</th></thead>";
            if (datos.str > 0) {
                numeroFormasPago = datos.str;
                for (var i = 0; i < datos.str; i++) {
                    if (
                        datos[i]["fmp_descripcion"] === "EFECTIVO" ||
                        datos[i]["es_transferencia"] === "SI"
                    ) {
                        TotalEgresos = datos[i]["TotalEgresos"];
                        TotalIngresos = datos[i]["TotalIngresos"];
                    } else if (datos[i]["fmp_descripcion"] == "CONSUMORECARGA") {
                        datos[i]["estadoSwitch"] = -1;
                    } else {
                        TotalEgresos = 0;
                        TotalIngresos = 0;
                    }

                    html +=
                        "<tr><td width='180' align='center' style='vertical-align:middle'>" +
                        datos[i]["fmp_descripcion"] +
                        "</td>";
                    //***********EGRESOS E INGRESOS*******************//
                    html += "<td><input inputmode='none' id='' readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value='" + TotalEgresos.toFixed(2) + "'></td>";
                    html += "<td><input inputmode='none' id='' readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value='" + TotalIngresos.toFixed(2) + "'></td>";
                    //************************************************//

                    html +=
                        "<td><input inputmode='none' id='' readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value='" +
                        datos[i]["totalRetirado"].toFixed(2) +
                        "'></td>";
                    $("#tpie tr:eq(0) td:eq(3)").html(
                        "$" + datos[i]["totalRetirado"].toFixed(2) + ""
                    );
                    $("#valorEfectivoTotal").val(datos[i]["totalRetirado"]);
                    $("#retiroEfectivoModalBilletes").val(
                        "$" + datos[i]["totalRetirado"].toFixed(2) + ""
                    );

                    html +=
                        "<td style='text-align:center;'><input inputmode='none' readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value=" +
                        datos[i]["Transacciones"] +
                        "></td>"; //

                    if (datos[i]["fmp_descripcion"] === "EFECTIVO") {
                        html +=
                            "<td><input inputmode='none' id='btnT" +
                            datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                            "'  style='width:112px; height:60px' type='button' class='btn btn-primary' value='Presione Aqui' onclick='fn_modal2(\"" +
                            datos[i]["fmp_id"] +
                            '","' +
                            datos[i]["ctrc_id"] +
                            '","' +
                            datos[i]["usr_id"] +
                            '",' +
                            datos[i]["totalRetirado"] +
                            "," +
                            datos[i]["Transacciones"] +
                            "," +
                            datos[i]["fpf_total_pagar"] +
                            ")'></td>";
                        idformaPago = datos[i]["fmp_id"];
                        $("#tpie3 tr:eq(2) td:eq(1)").html("");
                        $("#tpie3 tr:eq(2) td:eq(1)").html(
                            datos[i]["totalRetirado"].toFixed(2)
                        );
                        estadoSwitch = datos[i]["estadoSwitch"];
                    } else if (
                        datos[i]["fmp_descripcion"] == "CONSUMORECARGA" ||
                        datos[i]["fmp_descripcion"] == "FIDELIZACION" ||
                        datos[i]["fmp_descripcion"] == "VITALITY" ||
                        datos[i]["fmp_descripcion"] == "UBER" ||
                        datos[i]["fmp_descripcion"] == "CASHLESS" ||
                        datos[i]["fmp_descripcion"] == "CUPONEFECTIVO"

                    ) {
                        estadoSwitch = 100;
                        datos[i]["estadoSwitch"] = 100;

                        html +=
                            "<td><input inputmode='none' id='btnT" +
                            datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                            "' style='width:112px; height:60px' type='button' value='Presione Aqui' class='btn btn-primary'    onclick='fn_modalTarjetas(\"" +
                            datos[i]["fmp_id"] +
                            '","' +
                            datos[i]["ctrc_id"] +
                            '","' +
                            datos[i]["usr_id"] +
                            '",' +
                            datos[i]["totalRetirado"] +
                            "," +
                            datos[i]["Transacciones"] +
                            "," +
                            datos[i]["fpf_total_pagar"] +
                            "," +
                            datos[i]["diferenciaValor"] +
                            "," +
                            datos[i]["estadoSwitch"] +
                            ', "' +
                            datos[i]["fmp_descripcion"] +
                            '", ' +
                            datos[i]["fpf_total_pagar"] +
                            ")'></td>";
                    } else {
                        if (datos[i]["es_agregador"] == "SI") {
                            estadoSwitch = 100;
                            datos[i]["estadoSwitch"] = 100;

                            html +=
                                "<td><input inputmode='none' id='btnT" +
                                datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                                "' style='width:112px; height:60px' type='button' value='Presione Aqui' class='btn btn-primary'    onclick='fn_modalTarjetas(\"" +
                                datos[i]["fmp_id"] +
                                '","' +
                                datos[i]["ctrc_id"] +
                                '","' +
                                datos[i]["usr_id"] +
                                '",' +
                                datos[i]["totalRetirado"] +
                                "," +
                                datos[i]["Transacciones"] +
                                "," +
                                datos[i]["fpf_total_pagar"] +
                                "," +
                                datos[i]["diferenciaValor"] +
                                "," +
                                datos[i]["estadoSwitch"] +
                                ', "' +
                                datos[i]["fmp_descripcion"] +
                                '", ' +
                                datos[i]["fpf_total_pagar"] +
                                ")'></td>";
                        } else {
                            html +=
                                "<td><input inputmode='none' id='btnT" +
                                datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                                "' style='width:112px; height:60px' type='button' value='Presione Aqui' class='btn btn-primary'	onclick='fn_modalTarjetas(\"" +
                                datos[i]["fmp_id"] +
                                '","' +
                                datos[i]["ctrc_id"] +
                                '","' +
                                datos[i]["usr_id"] +
                                '",' +
                                datos[i]["totalRetirado"] +
                                "," +
                                datos[i]["Transacciones"] +
                                "," +
                                datos[i]["fpf_total_pagar"] +
                                "," +
                                datos[i]["diferenciaValor"] +
                                "," +
                                datos[i]["estadoSwitch"] +
                                ")'></td>";
                            estadoSwitch = datos[i]["estadoSwitch"];
                        }
                    }

                    html +=
                        "<td><input inputmode='none' readonly='readonly' class='form-control' style='width:105px; text-align:center; height:60px' type='text' type='text' id='" +
                        datos[i]["fmp_descripcion"] +
                        "' value='" +
                        datos[i]["fpf_total_pagar"].toFixed(2) +
                        "'></td>";

                    if (datos[i]["diferenciaValor"].toFixed(2) === "0.00") {
                        //alert("in "+datos[i]['diferenciaValor'].toFixed(2));
                        $("#botonCCL").html('<button type="button" id="btn_cajachicalocal1" class="btn btn-danger" style="height:70px;width:180px;">No puede realizar <br />Caja Chica Tienda <br /> con Diferencia en 0.00</button> ');
                    }

                    if (datos[i]["fmp_descripcion"] === "EFECTIVO") {
                        valorRetiroEfectivo = $("#valorEfectivoTotal").val();
                        valorTotalPos = datos[i]["fpf_total_pagar"];
                        diferencia = datos[i]["diferenciaValor"];
                        $("#hid_diferencia").val(diferencia);
                        $("#hid_masomenos").val("$" + diferencia.toFixed(2) + "");
                        $("#tpie3 tr:eq(1) td:eq(1)").html(diferencia.toFixed(2));

                        html +=
                            "<td><input inputmode='none' class='form-control' readonly='readonly' style='width:105px;text-align:center; height:60px' type='text' id='diferenciat" +
                            datos[i]["fmp_descripcion"] +
                            "' value = '" +
                            datos[i]["diferenciaValor"].toFixed(2) +
                            "' ></td>";
                    } else {
                        html +=
                            "<td><input inputmode='none' class='form-control' readonly='readonly' style='width:105px;text-align:center; height:60px' type='text' id='diferenciat" +
                            datos[i]["fmp_descripcion"] +
                            "' value = '" +
                            datos[i]["diferenciaValor"].toFixed(2) +
                            "'></td>";
                    }
                    html += "<input inputmode='none' type='hidden' value=" + datos[i]["fmp_id"] + "></tr>";
                    idcontrolEstacion = datos[i]["ctrc_id"];
                    $("#hid_controlEstacion").val(datos[i]["ctrc_id"]);
                    $("#formaPago").html(html);
                    id_botones[i] =
                        "btnT" + datos[i]["fmp_descripcion"].replace(/\s/g, "") + "";
                }

                //---------------------------------------CUPONES------------------------------------------------------//
                send = { consultaCupones: 1 };
                send.accion = 2;
                $.ajax({
                    async: false,
                    type: "GET",
                    dataType: "json",
                    contentType: "application/x-www-form-urlencoded",
                    url: "config_desmontadoCajero.php",
                    data: send,
                    success: function(datos) {
                        if (datos.str > 0) {
                            for (var j = 0; j < datos.str; j++) {
                                html +=
                                    "<tr><td width='180' align='center' style='vertical-align:middle'>" +
                                    datos[j]["fmp_descripcion"] +
                                    "</td>";
                                //***********EGRESOS E INGRESOS*******************//
                                html += "<td><input inputmode='none' id='' readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value='" + TotalEgresos.toFixed(2) + "'></td>";
                                html += "<td><input inputmode='none' id='' readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value='" + TotalIngresos.toFixed(2) + "'></td>";
                                //************************************************//
                                html += "<td><input inputmode='none' id='' readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value='0'></td>";
                                html +=
                                    "<td><input inputmode='none' id='' readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value='" +
                                    datos[j]["Transacciones"] +
                                    "'></td>";
                                html += "<td><input inputmode='none' id='' readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value='0'></td>";
                                html += "<td><input inputmode='none' id='' readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value='0'></td>";
                                html += "<td><input inputmode='none' id='' readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value='0'></td></tr>";
                            }
                            $("#formaPago").html(html);
                        }
                    }
                });

                fn_TarjetasDatafast(1);

                $("#modal_formaPago").modal('show');
                $("#hid_controlEfectivo").val(0);

                //FIN FUNCION BOTON CANCELAR AL DESMONTAR CAJERO
                fn_cargaTotal(idcontrolEstacion, idformaPago);
                $("#valorEfectivoTotal").val();
                $("#totalPosCalculado").val();
                diferenciaTotalFormasPago = parseFloat($("#valorEfectivoTotal").val()) - parseFloat($("#totalPosCalculado").val());
            } else {
                fn_TarjetasDatafast(0);
            }
        }
    });
}

//FUNCION BOTON CANCELAR AL DESMONTAR CAJERO
$("#btn_cancelargeneral").click(function() {
    $("#hid_controlEfectivo").val("0");
    if (arrayBilletes2.length > 0) {
        fn_eliminaBilletes(arrayBilletes2);
    }
    fn_eliminacortecaja(arrayBilletes2);
    $("#dialog").dialog("close");
    $("#modal_formaPago").modal("hide");
    $("#tpie tr:eq(0) td:eq(5)").html("");
    $("#hide_totalBilletes").val("");
});

function fn_desmontarCajeroArqueo() {
    
    hide_totalBilletes = $("#hide_totalBilletes").val();
    for (var i = 0; i < id_botones.length; i++) {
        if (isNaN($("#" + id_botones[i] + "").val())) {
            alertify.set({ labels: { ok: "OK" } });
            alertify.alert("Faltan valores por ingresar");
            return false;
        }
    }

    alertify.set({ labels: { ok: "SI", cancel: "NO" } });
    alertify.confirm("Esta seguro que desea Desasignar el Cajero?");

    $("#alertify-ok").click(function() {       
        const okButton = document.querySelector("#alertify-ok");
        okButton.classList.add("disabled-button");

        fn_eliminaFormasPagoAgregadasCashless();
        if (parseFloat($("#hid_controlDiferencia").val()) !== parseFloat(0)) {
            fpf_total_pagar = $("#hid_descuadre").val();

            send = { validaMontoDescuadre: 1 };
            send.accion = 1;
            send.fpf_total_pagar = parseFloat(fpf_total_pagar).toFixed(2);
            $.ajax({
                async: false,
                type: "GET",
                dataType: "json",
                contentType: "application/x-www-form-urlencoded",
                url: "config_desmontadoCajero.php",
                data: send,
                success: function(datos) {
                    if (datos.str > 0) {
                        for (var i = 0; i < datos.str; i++) {
                            if (datos[i]["validamontodescuadre"] === 1) {
                                $("#ModalMotivo").modal("show"); //alert("debo ingresar el motivo dl descuadre de valores");								
                                $("#ModalMotivo").on("shown.bs.modal", function() {
                                    $("#txtArea").focus();
                                });

                                $("#modal_formaPago").modal("hide");
                                $("#txtArea").val("");
                                $("#txtArea").focus(function() {
                                    fn_alfaNumerico(this);
                                });

                                fn_botonesDinamicosTecladoDescuadre();
                                $("#btn_okmotivo").click(function() {
                                    fn_cargando(1);
                                    area = $("#txtArea").val();
                                    if ($.trim(area) === "") {
                                        alertify.set({ labels: { ok: "OK" } });
                                        alertify.alert("Debe ingresar el motivo del descuadre de valores");
                                    } else {
                                        $("#modal_formaPago").modal("hide");
                                        send = { actualizaCajeroMotivo: 1 };
                                        send.accion = "I";
                                        send.accion_int = 1;
                                        send.usuario = $("#hid_usuario").val();
                                        send.motivoDescuadre = $("#txtArea").val();
                                        $.ajax({
                                            async: false,
                                            type: "GET",
                                            dataType: "json",
                                            contentType: "application/x-www-form-urlencoded",
                                            url: "config_desmontadoCajero.php",
                                            data: send,
                                            success: function(datos) {
                                                usuarioCajero = $("#hid_usuarioDescripcion").val();
                                                send = { auditoriaCajero: 1 };
                                                send.accion = "I";
                                                send.accion_int = 2;
                                                send.usuarioCajero = $("#hid_usuarioDescripcion").val();
                                                $.ajax({
                                                    async: false,
                                                    type: "GET",
                                                    dataType: "json",
                                                    contentType: "application/x-www-form-urlencoded",
                                                    url: "config_desmontadoCajero.php",
                                                    data: send,
                                                    success: function(datos) {
                                                        fn_imprimeDesmontadoCajero();

                                                        bandera = $("#bandera").val();
                                                        
                                                        //Ejecutar la interface de ventas
                                                        fn_generar_interface(1, bandera);
                                                    }
                                                });
                                            }
                                        });
                                    }
                                    fn_cargando(0);
                                    $("#modal_formaPago").modal("hide");
                                });

                                $("#btn_cmotivo").click(function() {
                                    $("#ModalMotivo").modal("hide");
                                    $("#modal_formaPago").modal("show");
                                });
                            } else {
                                $("#modal_formaPago").modal("hide");

                                send = { actualizaCajero: 1 };
                                send.accion = "U";
                                send.usuario = $("#hid_usuario").val();
                                $.ajax({
                                    async: false,
                                    type: "GET",
                                    dataType: "json",
                                    contentType: "application/x-www-form-urlencoded",
                                    url: "config_desmontadoCajero.php",
                                    data: send,
                                    success: function(datos) {
                                        usuarioCajero = $("#hid_usuarioDescripcion").val();
                                        send = { auditoriaCajero: 1 };
                                        send.accion = "I";
                                        send.accion_int = 2;
                                        send.usuarioCajero = $("#hid_usuarioDescripcion").val();
                                        $.ajax({
                                            async: false,
                                            type: "GET",
                                            dataType: "json",
                                            contentType: "application/x-www-form-urlencoded",
                                            url: "config_desmontadoCajero.php",
                                            data: send,
                                            success: function(datos) {
                                                fn_imprimeDesmontadoCajero();
                                                
                                                bandera = $("#bandera").val();

                                                //Ejecutar la interface de ventas
                                                fn_generar_interface(1, bandera);                
                                            }
                                        });
                                    }
                                });
                                $("#modal_formaPago").modal("hide");
                            }
                        }
                    }
                }
            });
        } else {
            $("#modal_formaPago").modal("hide");

            send = { actualizaCajero: 1 };
            send.accion = "U";
            send.usuario = $("#hid_usuario").val();
            $.ajax({
                async: false,
                type: "GET",
                dataType: "json",
                contentType: "application/x-www-form-urlencoded",
                url: "config_desmontadoCajero.php",
                data: send,
                success: function(datos) {
                    usuarioCajero = $("#hid_usuarioDescripcion").val();
                    send = { auditoriaCajero: 1 };
                    send.accion = "I";
                    send.accion_int = 2;
                    send.usuarioCajero = $("#hid_usuarioDescripcion").val();
                    $.ajax({
                        async: false,
                        type: "GET",
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded",
                        url: "config_desmontadoCajero.php",
                        data: send,
                        success: function(datos) {
                            fn_imprimeDesmontadoCajero();

                            bandera = $("#bandera").val();

                            //Ejecutar la interface de ventas
                            fn_generar_interface(1, bandera);
                        }
                    });
                }
            });
            $("#modal_formaPago").modal("hide");
        } 
    });
}

function fn_cargaTotal(codigo_ctrEstacion, idformaPago) {
    send = { consultatotalEstacion: 1 };
    send.accion = 1;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                $("#tpie tr:eq(0) td:eq(6)").html("$" + datos.total.toFixed(2));
                $("#totalPosCalculado").val(datos.total);
                $("#tpie tr:eq(0) td:eq(7)").html(
                    "$" + datos.totalDiferenciaFormaPagos.toFixed(2)
                );
                $("#tpie tr:eq(0) td:eq(4)").html(datos.transacciones);
                $("#tpie tr:eq(0) td:eq(1)").html("$" + datos.TotalEgresos.toFixed(2));
                $("#tpie tr:eq(0) td:eq(2)").html("$" + datos.TotalIngresos.toFixed(2));
            }
        }
    });

    send = { consultatotalformaPago: 1 };
    send.idforma = idformaPago;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                $("#totalPos").val("$" + datos.total.toFixed(2));
                $("#tpie3 tr:eq(3) td:eq(1)").html("");
                $("#tpie3 tr:eq(3) td:eq(1)").html(datos.total.toFixed(2));

                //valores desasignar cajero
                $("#tpie tr:eq(0) td:eq(3)").html("$" + datos.totalRetiros.toFixed(2));
                $("#tpie tr:eq(0) td:eq(5)").html("$" + datos.totalArqueo.toFixed(2));
            }
        }
    });
}

function fn_modalTarjetas(
    formaPago,
    ctrEstacion,
    id_usuario,
    totalRetirado,
    transacciones,
    posCalculadoValor,
    diferencia,
    estadoSwitch,
    nombre,
    valor
) {

    fn_apiAperturaCajon(formaPago)

    if (estadoSwitch != 100) {
        var send = { consultaTarjeta: 1 };
        send.accion = 1;
        send.idPago = formaPago;
        send.estadoRetiro = banderaDesmontado;
        send.estadoSwitch = estadoSwitch;
        $.ajax({
            async: false,
            type: "GET",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_desmontadoCajero.php",
            data: send,
            success: function(datos) {
                if (datos.str > 0) {
                    $("#dialogTarjetas").empty();
                    for (var i = 0; i < datos.str; i++) {
                        $("#titulomodalTarjeta").html(datos[i]["fmp_descripcion"]);
                        descripcionTarjeta = datos[i]["fmp_descripcion"];
                        totals = datos[i]["total"].toFixed(2);
                        arqueoTransacciones = $(
                            "#transaccionesIngresadas" +
                            datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                            ""
                        ).val();
                        var es_transferencia = datos[i]["es_transferencia"];

                        html = "<table align='center'><tr></tr>";
                        html +=
                            "<tr><th>Ingrese Monto: " +
                            datos[i]["fmp_descripcion"] +
                            "</th></tr>";
                        html += "<tr><td><input inputmode='none' id='txt_montoTarjeta' onkeypress='return fn_numeros(event)'  style='text-align:center; font-size:30px' type='text' value=" + totals + "></td></tr>";
                        html += "</table></br>";
                        html += "<table align='center'><tr><td><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumero('7') >7</button></td><td><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumero('8')>8</button></td><td><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumero('9')>9</button></td></tr><tr><td><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumero('4')>4</button></td><td><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumero('5')>5</button></td><td><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumero('6')>6</button></td></tr><tr><td><button class='btnVirtual'  style='font-size: 34px;' onclick=fn_agregarNumero('1')>1</button></td><td><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumero('2')>2</button></td><td><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumero('3')>3</button></td></tr><tr><td ><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumero('0')>0</button></td><td><button class='btnVirtualBorrarTarjetas' style='font-size: 34px;' id='btn_punto' onclick=fn_agregarNumero('.')>.</button></td><td><button class='btnVirtualBorrarTarjetas' style='font-size: 34px;' id='btn_borrar' onclick=fn_eliminarCantidad()>&larr;</button></td></tr><tr><td colspan='3'><button class='btnVirtualLimpiar' style='font-size: 34px;' id='btn_limpiar' onclick='fn_limpiarCantidad()'>LIMPIAR</button></td></tr></table><br/>";

                        //si la forma de pago es con pinpad o banda o si es de tipo transferencia de venta
                        if (
                            estadoSwitch == 1 ||
                            estadoSwitch == 2 ||
                            es_transferencia == "SI"
                        ) {
                            html +=
                                "<div class=''><div align='center'><button type='button' id='btn_okTarjeta" +
                                datos[i]["fmp_descripcion"] +
                                "' class='btn btn-primary ' style='height:65px;width:140px; font-size: 30px;' onclick='fn_guardarTarjetas(\"" +
                                formaPago +
                                '","' +
                                id_usuario +
                                '",' +
                                totalRetirado +
                                "," +
                                transacciones +
                                "," +
                                posCalculadoValor +
                                "," +
                                diferencia +
                                "," +
                                0 +
                                "," +
                                estadoSwitch +
                                ");' value='OK' >OK</button><button type='button' class='btn btn-default' id='btn_cancelTarjeta' style='height:65px;width:140px; font-size: 30px;' onclick='' data-dismiss='modal' value='Cancelar'>Cancelar</button></div></div>";
                        } else {
                            html +=
                                "<div class=''><div align='center'><button type='button' id='btn_okTarjeta" +
                                datos[i]["fmp_descripcion"] +
                                "' class='btn btn-primary ' style='height:65px;width:140px; font-size: 30px;' onclick='fn_guardarTarjetas(\"" +
                                formaPago +
                                '","' +
                                id_usuario +
                                '",' +
                                totalRetirado +
                                "," +
                                transacciones +
                                "," +
                                posCalculadoValor +
                                "," +
                                diferencia +
                                "," +
                                arqueoTransacciones +
                                "," +
                                estadoSwitch +
                                ");' value='OK' >OK</button><button type='button' class='btn btn-default' id='btn_cancelTarjeta' style='height:65px;width:140px; font-size: 30px;' onclick='' data-dismiss='modal' value='Cancelar'>Cancelar</button></div></div>";
                        }
                        $("#dialogTarjetas").append(html);
                    }

                    $("#ModalTarjetas").modal("show");

                    //si la forma de pago es con pinpad o banda o si es de tipo transferencia de venta
                    if (
                        estadoSwitch == 1 ||
                        estadoSwitch == 2 ||
                        es_transferencia == "SI"
                    ) {
                        $("#txt_montoTarjeta").attr("disabled", "disabled");
                        $(".btnVirtualBorrarTarjetas").attr("disabled", "disabled");
                        $(".btnVirtualLimpiar").attr("disabled", "disabled");
                        $(".btnVirtual").attr("disabled", "disabled");
                    }
                }
            }
        });
    } else {
        $("#dialogTarjetas").empty();
        $("#titulomodalTarjeta").html(nombre);
        descripcionTarjeta = nombre;
        totals = valor;
        arqueoTransacciones = $("#transaccionesIngresadas" + nombre.replace(/\s/g, "") + "").val();
        html = "<table align='center'><tr></tr>";
        html += "<tr><th>" + nombre + "</th></tr>";
        html += "<tr><td><input inputmode='none' id='txt_montoTarjeta' onkeypress='return fn_numeros(event)'  style='text-align:center; font-size:30px' type='text' value=" + totals + "></td></tr>";
        html += "</table></br>";
        html +=
            "<div class=''><div align='center'><button type='button' id='btn_okTarjeta" +
            nombre +
            "' class='btn btn-primary ' style='height:65px;width:140px; font-size: 30px;' onclick='fn_guardarTarjetas(\"" +
            formaPago +
            '","' +
            id_usuario +
            '",' +
            totalRetirado +
            "," +
            transacciones +
            "," +
            posCalculadoValor +
            "," +
            diferencia +
            "," +
            0 +
            "," +
            estadoSwitch +
            ");' value='OK' >OK</button><button type='button' class='btn btn-default' id='btn_cancelTarjeta' style='height:65px;width:140px; font-size: 30px;' data-dismiss='modal' value='Cancelar'>Cancelar</button></div></div>";
        $("#dialogTarjetas").append(html);
        $("#ModalTarjetas").modal("show");
        $("#txt_montoTarjeta").attr("disabled", "disabled");
    }

}

function fn_guardarTarjetas(formaPago, id_usuario, totalRetiradoA, transacciones, posCalculadoValor, diferencia, arqueoTransacciones, estadoSwitch) {

    if ($("#txt_montoTarjeta").val() == "" && banderaDesmontado == 2) {
        alertify.alert("Ingrese una cantidad");
        return false;
    }
    if (isNaN($("#txt_montoTarjeta").val())) {
        alertify.alert("Ingrese monto valido");
        return false;
    }

    totalTarjeta = $("#txt_montoTarjeta").val();

    //validacion para que no retiren mas dinero del calculado por el sistema
    if (banderaDesmontado == 1) {
        if (parseFloat(totalTarjeta) > Math.abs(parseFloat(diferencia))) {
            alertify.alert("La cantidad que intenta retirar es mayor a la calculada por el Sistema.");
            //$("#txt_montoTarjeta").val(posCalculadoValor);
            return false;
        }
    }

    if (totalTarjeta == "") {
        totalTarjeta = 0;
    }

    if (banderaDesmontado == 2) { //desasignar cajero
        if (diferencia != 0) {
            //diferencia = parseFloat(diferencia) + parseFloat(totalTarjeta);
            diferencia = Math.abs(parseFloat(posCalculadoValor)) - Math.abs(parseFloat(totalRetiradoA)) - Math.abs(parseFloat(totalTarjeta));
        }
    } else { //retiro y aqrueo
        if (diferencia == 0) {
            diferencia =
                parseFloat(totalRetiradoA) +
                parseFloat(totalTarjeta) -
                parseFloat(posCalculadoValor);
        } else {
            //diferencia = parseFloat(diferencia) + parseFloat(totalTarjeta);
            diferencia = Math.abs(parseFloat(posCalculadoValor)) - Math.abs(parseFloat(totalTarjeta)) - Math.abs(parseFloat(totalRetiradoA));
        }
    }

    codigo_formaPago = $("#hid_formaPago").val();
    idUsuario = $("#hid_usuario_efectivo").val();

    if (totalRetiradoA == 0) {
        totalRetirado = totalTarjeta;
    } else {
        if (banderaDesmontado == 1) { //retiro de dinero
            totalRetirado = parseFloat(totalRetiradoA) + parseFloat(totalTarjeta);
        } else { //desasignar cajero
            totalRetirado = totalTarjeta;
        }
    }

    send = { grabaarqueotarjeta: 1 };
    send.accion = "I";
    send.accion_int = 2;
    send.idPago = formaPago;
    send.totaltarjeta = totalTarjeta;
    send.idUser = id_usuario;
    send.totalRetirado = totalRetirado;
    send.transacciones = transacciones;
    send.posCalculadoValor = posCalculadoValor;
    send.diferencia = diferencia;
    send.ingresoManualFormaPago = 0; //bandera para saber si la forma de pago no es ingreso manual
    send.estadoRetiro = banderaDesmontado; //banderaDesmontado = 1 si es Retiro de dinero ; 2 si es Desasignar Cajero
    send.estadoPendiente = 3; //cuando el retiro de dinero esta pendiente
    send.estadoSwitch = estadoSwitch;

    if (banderaDesmontado == 3) {
        send.ingresoTransacciones = arqueoTransacciones;
    } else {
        send.ingresoTransacciones = 0;
    }
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            send = { auditoriaTarjeta: 1 };
            send.accion = "I";
            send.tipoTarjeta = descripcionTarjeta;
            send.totalTarjeta = totalTarjeta;
            send.banderafp = 0;
            $.ajax({
                async: false,
                type: "GET",
                dataType: "json",
                contentType: "application/x-www-form-urlencoded",
                url: "config_desmontadoCajero.php",
                data: send,
                success: function(datos) {}
            });

            send = { consultaformaPagoModificadoTarjeta: 1 };
            send.id_User = id_usuario;
            send.formaPago = formaPago;
            send.estadoRetiro = banderaDesmontado; //banderaDesmontado = 1 si es Retiro de dinero ; 2 si es Desasignar Cajero
            $.ajax({
                async: false,
                type: "GET",
                dataType: "json",
                contentType: "application/x-www-form-urlencoded",
                url: "config_desmontadoCajero.php",
                data: send,
                success: function(datos) {
                    totaleS = 0;
                    for (var i = 0; i < datos.str; i++) {
                        if (estadoSwitch == 3) {
                            $(
                                "#btnT" +
                                datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                                "_datafast"
                            ).val(datos[i]["retiroValor"].toFixed(2));
                            $(
                                "#btn" +
                                datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                                "_datafast"
                            ).val(datos[i]["retiroValor"].toFixed(2));
                            $("#diferenciat" + datos[i]["fmp_descripcion"] + "_datafast").val(
                                datos[i]["diferencia"].toFixed(2)
                            );
                        } else {
                            $(
                                "#btnT" + datos[i]["fmp_descripcion"].replace(/\s/g, "") + ""
                            ).val(datos[i]["retiroValor"].toFixed(2));
                            $(
                                "#btn" + datos[i]["fmp_descripcion"].replace(/\s/g, "") + ""
                            ).val(datos[i]["retiroValor"].toFixed(2));
                            $("#diferenciat" + datos[i]["fmp_descripcion"] + "").val(
                                datos[i]["diferencia"].toFixed(2)
                            );
                            $("#diferencia" + datos[i]["fmp_descripcion"] + "").val(
                                datos[i]["diferencia"].toFixed(2)
                            );
                        }
                    }
                }
            });
            $("#ModalTarjetas").modal("hide");
            fn_calculatotalesModificados(id_usuario);
        }
    });
}

function fn_modal2(codigo_formaPago, codigo_ctrEstacion, idUsuario, totalRetirado, transacciones, posCalculadoValor) {
    
    fn_apiAperturaCajon(codigo_formaPago);

    $("#totalRetirado").val(totalRetirado);
    $("#transacciones").val(transacciones);
    $("#posCalculadoValor").val(posCalculadoValor);
    $("#tpie3 tr:eq(0) td:eq(1)").html("");
    $("#hid_totalNuevo").val("");
    $("#tpie3 tr:eq(3) td:eq(1)").html(posCalculadoValor.toFixed(2));
    controla = $("#hid_controlEfectivo").val();
    if (controla === "0") {
        $("#hid_totalNuevo").val("");
        send = { consultaBilletes: 1 };
        send.accion = 3;
        $.ajax({
            async: false,
            type: "GET",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_desmontadoCajero.php",
            data: send,
            success: function(datos) {
                var html = "<thead><tr class='active'><th width='266px' colspan='2' style='text-align:center' class='tituloEtiqueta'>Denominaciones</th><th width='266px' class='tituloEtiqueta' style='text-align:center'>Cantidad</th><th width='266px' class='tituloEtiqueta' style='text-align:center'>Total</th></thead>";
                if (datos.str > 0) {
                    $("#billetes").empty();
                    miarray = new Array(datos.str);
                    for (var i = 0; i < datos.str; i++) {
                        if ($.trim(datos[i]["btd_Tipo"]) == "BILLETE") {
                            miarray[i] = "billete" + datos[i]["btd_Valor"] + "";
                            miarray[i] = miarray[i].replace(/\./g, "");
                            html +=
                                "<tr><td align='center'><img src='../imagenes/billete.png'/></td><td width='266px' style='text-align:center'><input inputmode='none' width='266px' class='form-control' align='center'  style='height:50px; text-align:center;' readonly='readonly' type='text' id=" +
                                miarray[i] +
                                "  value=" +
                                datos[i]["btd_Valor"].toFixed(2) +
                                "></td>";
                        } else {
                            miarray[i] = "moneda" + datos[i]["btd_Valor"] + "";
                            miarray[i] = miarray[i].replace(/\./g, "");
                            html +=
                                "<tr><td align='center'><img src='../imagenes/moneda.png'/></td><td width='266px' style='text-align:center'><input inputmode='none' width='266px' class='form-control' align='center'  style='height:50px; text-align:center;' readonly='readonly' type='text' id=" +
                                miarray[i] +
                                "  value=" +
                                datos[i]["btd_Valor"].toFixed(2) +
                                "></td>";
                        }
                        html +=
                            "<td width='266px' style='text-align:center'><input inputmode='none' class='form-control' align='center' width='266px' onkeypress='return fn_numeros(event)' maxlength='7' name = " +
                            datos[i]["btd_id"] +
                            " onclick='fn_borrarcantidad(\"" +
                            datos[i]["btd_id"] +
                            "\")' id='bi" +
                            miarray[i] +
                            "' align='right' style='height:50px; text-align:center;' type='text' value=0></td>";
                        html +=
                            "<td width='266px' style='text-align:center'><input inputmode='none' class='form-control' name = 't" +
                            datos[i]["btd_id"] +
                            "' align='center' width='266px' id='t" +
                            miarray[i] +
                            "' align='right' style='height:50px; text-align:center;' type='text' readonly='readonly' value=0.00></td>"; //
                        html +=
                            "<input inputmode='none' type='hidden' id='h" +
                            miarray[i] +
                            "' value=" +
                            datos[i]["btd_id"] +
                            ">";
                        html += "<input inputmode='none' type='hidden' id='h2" + miarray[i] + "' value=" + codigo_ctrEstacion + ">";
                        html += "<input inputmode='none' type='hidden' id='h3" + miarray[i] + "' value=" + idUsuario + "></tr>"; //VERIFICAR VARIABLE SESION USUARIO
                        $("#billetes").html(html);
                    }
                    diferencia = $("#hid_diferencia").val();
                    $("#hid_masomenos").val("$" + parseFloat(diferencia).toFixed(2) + "");
                    $("#tpie3 tr:eq(1) td:eq(1)").html("" + parseFloat(diferencia).toFixed(2) + "");
                    fn_calculaSubtotales(miarray, codigo_formaPago, idUsuario);
                }
            }
        });
    } else {
        send = { consultaBilletesModificados: 1 };
        send.accion = 1;
        $.ajax({
            async: false,
            type: "GET",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_desmontadoCajero.php",
            data: send,
            success: function(datos) {
                var html = "<thead><tr class='active'><th width='266px' colspan='2' style='text-align:center' class='tituloEtiqueta'>Denominaciones</th><th width='266px' class='tituloEtiqueta' style='text-align:center'>Cantidad</th><th width='266px' class='tituloEtiqueta' style='text-align:center'>Total</th></thead>";
                if (datos.str > 0) {
                    $("#billetes").empty();
                    miarray = new Array(datos.str);
                    for (var i = 0; i < datos.str; i++) {
                        if ($.trim(datos[i]["btd_Tipo"]) == "BILLETE") {
                            miarray[i] = "billete" + datos[i]["btd_Valor"] + "";
                            miarray[i] = miarray[i].replace(/\./g, "");
                            html +=
                                "<tr><td align='center'><img src='../imagenes/billete.png'/></td><td width='266px' style='text-align:center'><input inputmode='none' width='266px' class='form-control' align='center'  style='height:50px; text-align:center;' readonly='readonly' type='text' id=" +
                                miarray[i] +
                                "  value=" +
                                datos[i]["btd_Valor"].toFixed(2) +
                                "></td>";
                        } else {
                            miarray[i] = "moneda" + datos[i]["btd_Valor"] + "";
                            miarray[i] = miarray[i].replace(/\./g, "");
                            html +=
                                "<tr><td align='center'><img src='../imagenes/moneda.png'/></td><td width='266px' style='text-align:center'><input inputmode='none' width='266px' class='form-control' align='center'  style='height:50px; text-align:center;' readonly='readonly' type='text' id=" +
                                miarray[i] +
                                "  value=" +
                                datos[i]["btd_Valor"].toFixed(2) +
                                "></td>";
                        }
                        html +=
                            "<td style='text-align:center'><input inputmode='none' class='form-control' onkeypress='return fn_numeros(event)' id='bi" +
                            miarray[i] +
                            "'  maxlength='7' name = " +
                            datos[i]["btd_id"] +
                            " onclick='fn_borrarcantidad(\"" +
                            datos[i]["btd_id"] +
                            "\")' style='height:50px; text-align:center;' type='text' value=" +
                            datos[i]["bte_cantidad"] +
                            "></td>";
                        html +=
                            "<td style='text-align:center'><input inputmode='none' class='form-control' id='t" +
                            miarray[i] +
                            "' align='right' style='height:50px; text-align:center;' readonly='readonly' name ='t" +
                            datos[i]["btd_id"] +
                            "' type='text' value=" +
                            datos[i]["bte_total"] +
                            "></td>"; //
                        html +=
                            "<input inputmode='none' type='hidden' id='h" +
                            miarray[i] +
                            "' value=" +
                            datos[i]["btd_id"] +
                            ">";
                        html += "<input inputmode='none' type='hidden' id='h2" + miarray[i] + "' value=" + codigo_ctrEstacion + ">";
                        html += "<input inputmode='none' type='hidden' id='h3" + miarray[i] + "' value=" + idUsuario + "></tr>";
                        $("#billetes").html(html);
                    }
                    diferencia = $("#hid_diferencia").val();
                    $("#hid_masomenos").val("$" + parseFloat(diferencia).toFixed(2) + "");
                    $("#tpie3 tr:eq(1) td:eq(1)").html("" + parseFloat(diferencia).toFixed(2) + "");
                    fn_calculaSubtotalesMod(miarray, codigo_formaPago, idUsuario);
                }
            }
        });
    }
}

function fn_borrarcantidad(array) {
    $("input:text[name=" + array + "]").val("");
}

function fn_calculaSubtotalesMod(array, codigo_formaPago, idUsuario) {
    $("#hid_usuario_efectivo").val(idUsuario);
    $("#hid_formaPago").val(codigo_formaPago);
    $("#array").val(array);

    for (var i = 0; i < array.length; i++) {
        $("#bi" + array[i] + "").keypad();
    }

    for (var i = 0; i < subtotales.length; i++) {
        subtotales[i] = $("#t" + array[i] + "").val();
    }

    for (var i = 0; i < ocultos.length; i++) {
        ocultos[i] = $("#h" + array[i] + "").val();
    }

    for (var i = 0; i < ocultos2.length; i++) {
        ocultos2[i] = $("#h2" + array[i] + "").val();
    }

    for (var i = 0; i < ocultos3.length; i++) {
        ocultos3[i] = $("#h3" + array[i] + "").val();
    }

    $("#ModalBilletesDesmontarCajero").modal("show");

    $("#cancelar").click(function() {
        $("#dialog2").dialog("close");
        $("#modal_formaPago").modal("show");
    });

    $("#dialog2").dialog("open");

    for (var i = 0; i < array.length; i++) {
        $("#bi" + array[i] + "").focus(function() {
            for (var i = 0; i < denominaciones.length; i++) {
                denominaciones[i] = $("#" + array[i] + "").val();
            }

            for (var i = 0; i < cantidades.length; i++) {
                if ($("#bi" + array[i] + "").val() == "") {
                    $("#bi" + array[i] + "").val(0);
                }
                cantidades[i] = $("#bi" + array[i] + "").val();
            }

            for (var i = 0; i < resultado.length; i++) {
                resultado[i] = denominaciones[i] * cantidades[i];
            }

            suma = 0;
            for (var i = 0; i < array.length; i++) {
                $("#t" + array[i] + "").val(resultado[i].toFixed(2));
                suma += parseFloat(resultado[i]);
            }

            suma2 = suma; //totalNuevo
            $("#hid_totalNuevo").val(suma2);
            $("#tpie3 tr:eq(0) td:eq(1)").html("" + suma2.toFixed(2));

            dif = $("#hid_diferencia").val();
            dif_Total = parseFloat(dif) + parseFloat(suma2);
            $("#hid_masomenos").val(dif_Total.toFixed(2));
            $("#tpie3 tr:eq(1) td:eq(1)").html(dif_Total.toFixed(2));

            if (dif_Total == 0) {
                $("#tr_masomenos").addClass("success");
                $("#tr_masomenos").removeClass("danger");
            } else {
                $("#tr_masomenos").addClass("danger");
                $("#tr_masomenos").removeClass("success");
            }
        });

        valorsumabilletes = $("#valorsumabilletes").val();
        valormasomenos = $("#valormasomenos").val();
        $("#tpie3 tr:eq(0) td:eq(1)").html(valorsumabilletes);
        $("#tpie3 tr:eq(1) td:eq(1)").html(valormasomenos);

        input_dif_Total = $("#hid_masomenos").val();

        if (parseFloat(input_dif_Total) == 0) {
            $("#tr_masomenos").addClass("success");
            $("#tr_masomenos").removeClass("danger");
        } else {
            $("#tr_masomenos").addClass("danger");
            $("#tr_masomenos").removeClass("success");
        }
    }
}

function fn_calculaSubtotales(array, codigo_formaPago, idUsuario) {
    $("#hid_usuario_efectivo").val(idUsuario);
    $("#hid_formaPago").val(codigo_formaPago);
    $("#array").val(array);

    for (var i = 0; i < array.length; i++) {
        $("#bi" + array[i] + "").keypad();
    }

    for (var i = 0; i < subtotales.length; i++) {
        subtotales[i] = $("#t" + array[i] + "").val();
    }

    for (var i = 0; i < ocultos.length; i++) {
        ocultos[i] = $("#h" + array[i] + "").val();
    }

    for (var i = 0; i < ocultos2.length; i++) {
        ocultos2[i] = $("#h2" + array[i] + "").val();
    }

    for (var i = 0; i < ocultos3.length; i++) {
        ocultos3[i] = $("#h3" + array[i] + "").val();
    }

    for (var i = 0; i < array.length; i++) {
        $("#" + array[i] + "").blur();
    }

    $("#ModalBilletesDesmontarCajero").modal("show");

    $("#cancelar").click(function() {
        $("#dialog2").dialog("close");
        $("#modal_formaPago").modal("show");
    });

    $("#dialog2").dialog("open");

    for (var i = 0; i < array.length; i++) { //inicio del FOR
        $("#bi" + array[i] + "").focus(function() { //inicio evento focus
            for (var i = 0; i < denominaciones.length; i++) {
                denominaciones[i] = $("#" + array[i] + "").val();
            }

            for (var i = 0; i < cantidades.length; i++) {
                if ($("#bi" + array[i] + "").val() == "") {
                    $("#bi" + array[i] + "").val(0);
                }
                cantidades[i] = $("#bi" + array[i] + "").val();
            }

            for (var i = 0; i < resultado.length; i++) {
                resultado[i] = denominaciones[i] * cantidades[i];
            }

            suma = 0;
            for (var i = 0; i < array.length; i++) {
                $("#t" + array[i] + "").val(resultado[i].toFixed(2));
                suma += parseFloat(resultado[i]);
            }

            suma2 = suma; //totalNuevo
            $("#hid_totalNuevo").val(suma2);
            $("#tpie3 tr:eq(0) td:eq(1)").html("" + suma2.toFixed(2));

            dif = $("#hid_diferencia").val();
            dif_Total = parseFloat(dif) + parseFloat(suma2);
            $("#hid_masomenos").val(dif_Total.toFixed(2));
            $("#tpie3 tr:eq(1) td:eq(1)").html(dif_Total.toFixed(2));

            if (dif_Total == 0) {
                $("#tr_masomenos").addClass("success");
                $("#tr_masomenos").removeClass("danger");
            } else {
                $("#tr_masomenos").addClass("danger");
                $("#tr_masomenos").removeClass("success");
            }

        });
        valorsumabilletes = $("#valorsumabilletes").val();
        valormasomenos = $("#valormasomenos").val();
        input_dif_Total = $("#hid_masomenos").val();

        if (parseFloat(input_dif_Total) == 0) {
            $("#tr_masomenos").addClass("success");
            $("#tr_masomenos").removeClass("danger");
        } else {
            $("#tr_masomenos").addClass("danger");
            $("#tr_masomenos").removeClass("success");
        }
    }
}
$("#ok_BilletesEfectivo").click(function() {
    if (banderaDesmontado === 1)
        $("#botonCCL").html('<button type="button" id="btn_cajachicalocal1" class="btn btn-danger" style="height:70px;width:180px;">No puede realizar <br />Caja Chica Local <br /> con Valores Declarados</button> ');
});

$("#btn_okEfectivo").click(function() {
    if (banderaDesmontado === 1) {
        $("#botonCCL").html('<button type="button" id="btn_cajachicalocal" class="btn btn-warning" style="height:70px;width:180px;" onclick="fn_modal_cajaChicaLocal();" >Caja Chica <br /> Tienda</button>');
        estadoCCL = 0;
    }
});

function fn_guardaTotalesBilletes() {
    //$("#btn_cajachicalocal").hide();
    valorsumabilletes = $("#tpie3 tr:eq(0) td:eq(1)").text();
    valormasomenos = $("#tpie3 tr:eq(1) td:eq(1)").text();
    $("#valorsumabilletes").val(valorsumabilletes);
    $("#valormasomenos").val(valormasomenos);

    if ($("#hid_totalNuevo").val() == "") {
        alertify.alert("Ingrese cantidades");
        return false;
    }

    resultado = $.grep(resultado, function(n) {
        return n == 0 || n;
    }); //eliminar posiciones vacias de un array//cp
    $("#hid_controlEfectivo").val(1);
    totalNuevo = $("#hid_totalNuevo").val();

    send = { grabaBilletes: 1 };
    send.accion = "I2";
    send.cantidades2 = cantidades;
    send.resultado2 = resultado;
    send.oculto = ocultos;
    send.oculto2 = ocultos2;
    send.oculto3 = ocultos3;
    send.tipoEfectivo = 0;
    send.banderaDesmontado = banderaDesmontado;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            controla = $("#hid_controlEfectivo").val();
            if (controla == 0) {
                send = { auditoriaEfectivo: 1 };
                send.accion = "I";
                send.auditoriaTotal = totalNuevo.substring(1);
                $.ajax({
                    async: false,
                    type: "GET",
                    dataType: "json",
                    contentType: "application/x-www-form-urlencoded",
                    url: "config_desmontadoCajero.php",
                    data: send,
                    success: function(datos) {}
                });
            }
            array = $("#array").val();
            send = { consultaidBilletes: 1 };
            send.accion = 2;
            send.top = array.length;
            $.ajax({
                async: false,
                type: "GET",
                dataType: "json",
                contentType: "application/x-www-form-urlencoded",
                url: "config_desmontadoCajero.php",
                data: send,
                success: function(datos) {
                    arrayBilletes = new Array(datos.str);
                    for (var i = 0; i < datos.str; i++) {
                        arrayBilletes[i] = "" + datos[i]["bte_id"] + "";
                    }
                    arrayBilletes2 = arrayBilletes;
                }
            });

            totalRetirado = $("#totalRetirado").val();
            transacciones = $("#transacciones").val();
            posCalculadoValor = $("#posCalculadoValor").val();
            diferencia = $("#tpie3 tr:eq(1) td:eq(1)").text();
            codigo_formaPago = $("#hid_formaPago").val();
            idUsuario = $("#hid_usuario_efectivo").val();
            send = { grabaArqueo: 1 };
            send.accion = "I";
            send.accion_int = 1;
            send.resNuevo = totalRetirado;
            send.formaPago = codigo_formaPago;
            send.idUsuario = idUsuario;
            send.totalRetirado = totalNuevo;
            send.transacciones = transacciones;
            send.posCalculadoValor = posCalculadoValor;
            send.diferencia = diferencia;
            send.estadoRetiro = banderaDesmontado; //estado 2 cuando es desmontado total de cajero
            send.estadoPendiente = 3; //cuando el desmontado de dinero esta pendiente
            send.arqueoTransacciones = 0;
            send.estadoSwitch = estadoSwitch;
            $.ajax({
                async: false,
                type: "POST",
                dataType: "json",
                contentType: "application/x-www-form-urlencoded",
                url: "config_desmontadoCajero.php",
                data: send,
                success: function(datos) {
                    $("#hid_controlEfectivo").val(1);
                    send = { consultaformaPagoModificado: 1 };
                    send.accion = 1;
                    send.id_usuariO = idUsuario;
                    send.formaPago = codigo_formaPago;
                    send.estadoRetiro = banderaDesmontado; //banderaDesmontado = 1 si es Retiro de dinero ; 2 si es Desasignar Cajero
                    $.ajax({
                        async: false,
                        type: "GET",
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded",
                        url: "config_desmontadoCajero.php",
                        data: send,
                        success: function(datos) {
                            if (datos.str > 0) {
                                $("#diferenciat" + datos.fmp_descripcion + "").empty();
                                for (var i = 0; i < datos.str; i++) {
                                    $("#diferenciat" + datos[i]["fmp_descripcion"] + "").val(
                                        datos[i]["diferencia"].toFixed(2)
                                    );
                                    $("#btnT" + datos[i]["fmp_descripcion"] + "").val(
                                        datos[i]["retiroValor"].toFixed(2)
                                    );
                                }

                            }
                            $("#ModalBilletesDesmontarCajero").modal("hide");
                            $("#modal_formaPago").modal("show");
                            fn_calculatotalesModificados(idUsuario);
                        }
                    });
                }
            });
        }
    });
}

function fn_calculatotalesModificados(id_usuario) {
    var send;
    send = { calculatotalesModificados: 1 };
    send.userId = id_usuario;
    send.accion = 1; //totales Retiros
    send.estadoRetiro = banderaDesmontado; //banderaDesmontado = 1 si es Retiro de dinero ; 2 si es Desasignar Cajero
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            //valores retiro de dinero
            $("#tpieEfectivo tr:eq(0) td:eq(6)").html(
                "$" + datos.retiroValor.toFixed(2)
            );
            $("#tpieEfectivo tr:eq(0) td:eq(8)").html(
                "$" + datos.diferencia.toFixed(2)
            );

            //desasignar cajero
            $("#tpie tr:eq(0) td:eq(5)").html("$" + datos.retiroValor.toFixed(2));
            $("#tpie tr:eq(0) td:eq(7)").html("$" + datos.diferencia.toFixed(2));
            $("#hid_controlDiferencia").val(parseFloat(datos.diferencia));
            $("#hid_descuadre").val(parseFloat(datos.diferencia));
        }
    });
}

function fn_totalesIngresados(id_usuarioo) {
    var send;
    send = { totalesIngresados: 1 };
    send.User = id_usuarioo;
    send.accion = 1;
    send.banderaDesmontado = banderaDesmontado;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            for (var i = 0; i < datos.str; i++) {
                if (datos[i]["arc_valor"] != null) {
                    //valores Desasignar Cajero
                    $("#btnT" + datos[i]["fmp_descripcion"].replace(/\s/g, "") + "").val(
                        datos[i]["arc_valor"].toFixed(2)
                    );
                }
                banderaArqueoTarjeta = 1;
            }
        }
    });
}

function fn_totalesIngresadosEfectivo(id_usuarioo, totalBilletes) {
    var send;
    send = { totalesIngresados: 1 };
    send.User = id_usuarioo;
    send.accion = 2;
    send.banderaDesmontado = banderaDesmontado;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            for (var i = 0; i < datos.str; i++) {
                $("#btn" + datos[i]["fmp_descripcion"].replace(/\s/g, "") + "").val(
                    totalBilletes
                );
                $(
                    "#btnArqueo" + datos[i]["fmp_descripcion"].replace(/\s/g, "") + ""
                ).val(totalBilletes);
            }
        }
    });
}

function fn_eliminaBilletes(arrayBilletes2) {
    var send;
    send = { eliminaBilletes: 1 };
    send.accion = "D";
    send.eliminaBillete = arrayBilletes2;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {}
    });
}

function fn_eliminacortecaja(billetes) {
    var send;
    billete = billetes;
    send = { eliminaArqueo: 1 };
    send.usuarioID = $("#hid_usuario").val();
    send.estadoRetiro = banderaDesmontado;
    send.estadoSwitch = 0;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {}
    });
}

function fn_desmontadoDirecto() {
    send = { existeCuentaAbiertaMesa: 1 };
    send.accion = 2;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                if (datos.cuentaAbiertaMesa == 0) {
                    send = { existeCuentaAbierta: 1 };
                    send.accion = 1;
                    $.ajax({
                        async: false,
                        type: "GET",
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded",
                        url: "config_desmontadoCajero.php",
                        data: send,
                        success: function(datos) {
                            if (datos.str > 0) {
                                if (datos.cuentaAbierta == 1) {
                                    nombreUsr = $("#hid_usuarioDescripcion").val();
                                    alertify.alert("No puede desasignar el cajero porque el usuario (" + nombreUsr + ") tiene cuentas abiertas");
                                    $("#alertify-ok").click(function() {
                                        bandera = $("#bandera").val();
                                        if (bandera == "Inicio") {
                                            window.location.replace("../index.php");
                                        } else {
                                            window.location.replace("corteCaja.php");
                                        }
                                    });
                                } else {
                                    send = { retirofondo: 1 };
                                    send.accion = 1;
                                    $.ajax({
                                        async: false,
                                        type: "GET",
                                        dataType: "json",
                                        contentType: "application/x-www-form-urlencoded",
                                        url: "config_desmontadoCajero.php",
                                        data: send,
                                        success: function(datos) {
                                            if (datos.str > 0) {
                                                if (datos.retirofondo === 1) {
                                                    alertify.confirm("Esta seguro que desea Desasignar el Cajero??");
                                                    $("#alertify-ok").click(function() {
                                                        send = { DesmontadoDirecto: 1 };
                                                        send.accion = "U";
                                                        $.ajax({
                                                            async: false,
                                                            type: "GET",
                                                            dataType: "json",
                                                            contentType: "application/x-www-form-urlencoded",
                                                            url: "config_desmontadoCajero.php",
                                                            data: send,
                                                            success: function(datos) {
                                                                alertify.alert("Desmontado exitoso");
                                                                $("#alertify-ok").click(function() {
                                                                    bandera = $("#bandera").val();
                                                                    if (bandera == "Inicio") {
                                                                        window.location.replace("../index.php");
                                                                    } else {
                                                                        window.location.replace("corteCaja.php");
                                                                    }
                                                                });
                                                            }
                                                        });
                                                    });
                                                } else if (datos.retirofondo === 2) {
                                                    alertify.alert("No puede desasignar el cajero porque tiene pedidos DOMICILIO sin facturar");
                                                } else {
                                                    alertify.set({ labels: { ok: "SI", cancel: "NO" } });
                                                    alertify.confirm("No puede desasignar el cajero porque el Administrador no ha retirado el fondo. Desea retirarlo?");
                                                    $("#alertify-ok").click(function() {
                                                        window.location.replace("../retiro_fondo/retiro_fondo.php");
                                                    });
                                                }
                                            }
                                        }
                                    });
                                }
                            }
                        }
                    });
                } else {
                    nombreUsr = $("#hid_usuarioDescripcion").val();
                    alertify.alert("No puede desasignar el cajero porque el usuario (" + nombreUsr + ") tiene mesas abiertas");
                    $("#alertify-ok").click(function() {
                        bandera = $("#bandera").val();
                        if (bandera == "Inicio") {
                            window.location.replace("../index.php");
                        } else {
                            window.location.replace("corteCaja.php");
                        }
                    });
                }
            }
        }
    });
}

function fn_agregarNumero(valor) {
    var cantidad = $("#txt_montoTarjeta").val();
    if (cantidad.length < 8) {
        //presionamos la primera vez punto
        if ((cantidad == "" || cantidad == 0) && valor == ".") {
            //si escribimos una coma al principio del número
            $("#txt_montoTarjeta").val("0."); //escribimos 0.
            coma = 1;
        } else {
            //continuar escribiendo un número
            if (valor == ".") {
                //si escribimos una coma decimal por primera vez; si intentamos escribir una segunda coma decimal no realiza ninguna acción.
                if (coma == 0) {
                    cantidad = cantidad + "" + valor;
                    $("#txt_montoTarjeta").val(cantidad);
                    coma = 1; //cambiar el estado de la coma
                }
            }
            //Resto de casos: escribir un número del 0 al 9:
            else {
                var variable = cantidad;
                var indice = 0;
                indice = variable.indexOf(".") + 1;
                if (indice > 0) {
                    variable = variable.substring(indice, variable.length);
                    if (variable.length <= 2) {
                        cantidad = cantidad + "" + valor;
                        $("#txt_montoTarjeta").val(cantidad);
                        fn_focusTarjeta();
                    }
                } else {
                    cantidad = cantidad + "" + valor;
                    $("#txt_montoTarjeta").val(cantidad);
                    fn_focusTarjeta();
                }
            }
        }
    }
    fn_focusTarjeta();
}

function fn_focusTarjeta() {
    $("#txt_montoTarjeta").focus();
}

function fn_eliminarCantidad() {
    var lc_cantidad = document.getElementById("txt_montoTarjeta").value.substring(0, document.getElementById("txt_montoTarjeta").value.length - 1);

    if (lc_cantidad == "" || lc_cantidad == ".") {
        coma = 0;
    }
    document.getElementById("txt_montoTarjeta").value = lc_cantidad;
    fn_focusTarjeta();
}

function fn_imprimeDesmontadoCajero() {
    var send;
    usr_id = $("#hid_usuario").val();
    ctrc_id = $("#hid_controlEstacion").val();
    send = { traeUsuarioAdmin: 1 };
    send.accion = 5;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                usr_id_admin = datos.usr_id;
                send = { imprimeDesmontadoCajero: 1 };
                send.usr_id = usr_id;
                send.ctrc_id = ctrc_id;
                send.usr_id_admin = usr_id_admin;
                var apiImpresion = getConfiguracionesApiImpresion();
                // console.log(apiImpresion);        
                if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
                    fn_cargando(1);    
                    var result = new apiServicioImpresion('desmontadoCajero',null,null, send);
                    var imprime = result["imprime"];
                    var mensaje = result["mensaje"];

                    console.log('imprime: ', imprime);
                    console.log('imprime: ', mensaje);

                    if (!imprime) {
                        alertify.success('Imprimiendo Desmontado de Cajero...');
                        
                    } else {
                        alertify.alert(mensaje);

                        alertify.success('Error al imprimir Desmontado de Cajero...');
                        fn_cargando(0);
                    }

                }else{

                    $.ajax({
                        async: false,
                        type: "GET",
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded",
                        url: "config_desmontadoCajero.php",
                        data: send,
                        success: function(datos) {}
                    });
                }
            }
        }
    });
}
//================================================================================//
// FUNCION PARA VALIDAR CUENTAS ABIERTAS Y TRAER LA FORMA DE PAGO EFECTIVO        //
//================================================================================//
function fn_validarCuentasAbiertasRetiroEfectivo(banderaTipoCorte) {
    let unificacion=$("#unificacion_transferencia_de_venta").val();
    if(unificacion==1){
        
    }else {
        if (debeRealizarTransferencia && valorTotalTransferenciaVenta > 0) {
            if (tipoLocalTransferenciaVenta === "ORIGEN") {
                alertify.alert("Antes de desasignar el cajero/a, debe realizar la transferencia de venta : " + valorTotalTransferenciaVenta);
                $("#alertify-ok").click(function() {});

            }

            if (tipoLocalTransferenciaVenta === "DESTINO") {
                alertify.alert("Antes de desasignar debe recibir la transferencia de venta: " + valorTotalTransferenciaVentaHeladeria);
                $("#alertify-ok").click(function() {
                    location.reload();
                });

            }

            return false;
        }
    }
    //banderaTipoCorte = 1 para retiro de dinero ; 3 para Arqueo de dinero
    if (banderaTipoCorte == 1) {
        $("#titulomodalRetiros").html("RETIROS");
        $("#btn_FondoCaja").css("display", "none");
    } else if (banderaTipoCorte == 3) {
        $("#titulomodalRetiros").html("ARQUEO");
        $("#btn_cajachicalocal").remove();
        $("#btn_cajachicalocal1").remove();
        $("#btn_cajachicalocal2").remove();
        $("#btn_FondoCaja").css("display", "block");
        /*trae el valor del fondo de caja asignado en caso de existir*/
        var send = { consultaFondoAsignado: 1 };
        
        $.ajax({
            async: false,
            url: "config_desmontadoCajero.php",
            data: send,
            dataType: "json",
            success: function(datos) {
                if (datos.str > 0) {
                    $("#btn_FondoCaja").html("Fondo Asignado: " + datos[0].fondo);

                } else {
                    $("#btn_FondoCaja").html("0.00");
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                $("#btn_FondoCaja").css("display", "none");
                alertify.alert("ERROR AL LEER INFORMACION DEL FONDO DE CAJA ASIGNADO.");
            }
        });
    }
    banderaDesmontado = banderaTipoCorte;
    id_botones.length = 0; //vaciamos el arreglo    

    //Limpiamos las cajas de texto que almacenamos los valores de formas de pago
    $("#totalRetirado").val("");
    $("#transacciones").val("");
    $("#posCalculadoValor").val("");
    $("#diferencia").text("");
    $("#hid_formaPago").val("");
    $("#hid_usuario_efectivo").val("");
    fn_formaPagoEfectivo(banderaDesmontado);
}
//================================================================================//
// FUNCION QUE PERMITE TRAER LA FORMA DE PAGO EFECTIVO PARA EL RETIRO DE EFECTIVO //
//================================================================================//
function fn_formaPagoEfectivo(banderaDesmontado) {
    $("#tpieEfectivo tr:eq(0) td:eq(1)").html("");
    $("#tpieEfectivo tr:eq(0) td:eq(2)").html("");
    $("#tpieEfectivo tr:eq(0) td:eq(3)").html("");
    $("#tpieEfectivo tr:eq(0) td:eq(4)").html("");
    $("#tpieEfectivo tr:eq(0) td:eq(5)").html("");
    $("#tpieEfectivo tr:eq(0) td:eq(6)").html("");
    $("#hid_controlRetiroEfectivo").val(0);

    send = { consultaformaPagoEfectivo: 1 };
    send.accion = 1;
    idformaPago = 0;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            if (banderaDesmontado == 3) { //solo para arqueo retiro de dinero
                var html = "<thead>\n\
                        <tr class='active'>\n\
                            <th width='' style='text-align:center'>Formas de Pago</th>\n\
                            <th width='' style='text-align:center'>Egresos</th>\n\
                            <th width='' style='text-align:center'>Ingresos</th>\n\
                            <th width='' style='text-align:center'>Retirado</th>\n\
                            <th width='' style='text-align:center'>Transac.</th>\n\
                            \n\<th width='' style='text-align:center'>Ingreso Transac.</th>\n\
                            <th style='text-align:center' width=''>Valor Declarado</th>\n\
                            <th style='text-align:center' width=''>POS Calculado</th>\n\
                            <th style='text-align:center' width=''>Diferencia</th>\n\
                        </tr>\n\
                   </thead>";
                $("#totalArqueoTransacciones").show();
            } else { //retiro de dinero
                var html = "<thead>\n\
                        <tr class='active'>\n\
                            <th width='130px' style='text-align:center'>Formas de Pago</th>\n\
                            <th width='' style='text-align:center'>Egresos</th>\n\
                            <th width='' style='text-align:center'>Ingresos</th>\n\
                            <th width='' style='text-align:center'>Retirado</th>\n\
                            <th width='' style='text-align:center'>Transacciones</th>\n\
                            <th style='text-align:center' width=''>Valor Declarado</th>\n\
                            <th style='text-align:center' width=''>POS Calculado</th>\n\
                            <th style='text-align:center' width='130px'>Diferencia</th>\n\
                        </tr>\n\
                   </thead>";
                $("#totalArqueoTransacciones").hide();
            }

            if (datos.str > 0) {
                numeroFormasPago = datos.str;
                arrayArqueo = new Array(datos.str);
                for (var i = 0; i < datos.str; i++) {

                    arrayArqueo[i] = datos[i]["fmp_descripcion"].replace(/\s/g, ""); //almacenamos en un arreglo la descripcion de las formas de pago
                    if (datos[i]["fmp_descripcion"] === "EFECTIVO") {
                        TotalEgresos = datos[i]["TotalEgresos"];
                        TotalIngresos = datos[i]["TotalIngresos"];
                    } else {
                        TotalEgresos = 0;
                        TotalIngresos = 0;
                    }
                    html +=
                        "<tr><td width='180px' align='center' style='vertical-align:middle'>" +
                        datos[i]["fmp_descripcion"] +
                        "</td>";

                    if (banderaDesmontado == 3) { //arqueo retiro de dinero
                        //***********EGRESOS E INGRESOS*******************//
                        html += "<td><input inputmode='none'  id='' readonly='readonly' style='width:95px; height:60px; text-align:center;' class='form-control' value='" + TotalEgresos.toFixed(2) + "'></td>";
                        html += "<td><input inputmode='none' id='' readonly='readonly' style='width:95px; height:60px; text-align:center;' class='form-control' value='" + TotalIngresos.toFixed(2) + "'></td>";
                        //************************************************//
                        html +=
                            "<td><input inputmode='none' id='retirado_" +
                            datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                            "' readonly='readonly' style='width:95px; height:65px; text-align:center;' class='form-control' value=" +
                            datos[i]["arc_valor"].toFixed(2) +
                            "></td>"; //
                        html +=
                            "<td><input inputmode='none' id='transacciones_" +
                            datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                            "' readonly='readonly' style='width:70px; height:65px; text-align:center;' class='form-control' value=" +
                            datos[i]["Transacciones"] +
                            "></td>"; //

                        if (datos[i]["fmp_descripcion"] == "EFECTIVO") {
                            html +=
                                "<td><input inputmode='none' id='transaccionesIngresadas" +
                                datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                                "' onkeypress='fn_numeros(event);' name='transacciones_" +
                                datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                                "' onclick='fn_borrarcantidadTeclado(\"" +
                                datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                                "\");' style='width:70px; height:65px; text-align:center;' class='form-control' value='0'></td>"; //
                            html +=
                                "<td><input inputmode='none' id='btnArqueo" +
                                datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                                "'  style='width:112px; height:65px' type='button' class='btn btn-primary' value='Presione Aqui' onclick='fn_modalBilletesEfectivo(\"" +
                                datos[i]["fmp_id"] +
                                '","' +
                                datos[i]["ctrc_id"] +
                                '","' +
                                datos[i]["usr_id"] +
                                '",' +
                                datos[i]["arc_valor"] +
                                "," +
                                datos[i]["Transacciones"] +
                                "," +
                                datos[i]["fpf_total_pagar"] +
                                "," +
                                datos[i]["diferencia"] +
                                ',"' +
                                formaPagoDescripcion +
                                '",' +
                                datos[i]["TransaccionesIngresadas"] +
                                ", " +
                                datos[i]["estadoSwitch"] +
                                ")'></td>";
                            estadoSwitch = datos[i]["estadoSwitch"];
                        } else if (
                            datos[i]["fmp_descripcion"] == "CONSUMORECARGA" ||
                            datos[i]["fmp_descripcion"] == "FIDELIZACION" ||
                            datos[i]["fmp_descripcion"] == "VITALITY" ||
                            datos[i]["fmp_descripcion"] == "CUPONEFECTIVO" ||
                            datos[i]["es_agregador"] == "SI"
                        ) {
                            datos[i]["estadoSwitch"] = 100;
                            //html += "<td><input id='transaccionesIngresadas" + datos[i]['fmp_descripcion'].replace(/\s/g, "") + "' readonly='readonly' name='transacciones_" + datos[i]['fmp_descripcion'].replace(/\s/g, "") + "' style='width:70px; height:65px; text-align:center;' class='form-control' value='" + datos[i]['Transacciones'] + "'></td>";
                            html +=
                                "<td><input inputmode='none' id='transacciones_" +
                                datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                                "' readonly='readonly' style='width:70px; height:65px; text-align:center;' class='form-control' value=" +
                                datos[i]["Transacciones"] +
                                "></td>";
                            html +=
                                "<td><input inputmode='none' id='btn" +
                                datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                                "' style='width:112px; height:60px' type='button' value='Presione Aqui' class='btn btn-primary'    onclick='fn_modalTarjetas(\"" +
                                datos[i]["fmp_id"] +
                                '","' +
                                datos[i]["ctrc_id"] +
                                '","' +
                                datos[i]["usr_id"] +
                                '",' +
                                datos[i]["arc_valor"] +
                                "," +
                                datos[i]["Transacciones"] +
                                "," +
                                datos[i]["fpf_total_pagar"] +
                                "," +
                                datos[i]["diferencia"] +
                                "," +
                                datos[i]["estadoSwitch"] +
                                ', "' +
                                datos[i]["fmp_descripcion"] +
                                '", ' +
                                datos[i]["fpf_total_pagar"] +
                                ")'></td>";
                        } else {
                            if (datos[i]["es_agregador"] == "SI") {
                                estadoSwitch = 100;
                                datos[i]["estadoSwitch"] = 100;

                                html +=
                                    "<td><input inputmode='none' id='transaccionesIngresadas" +
                                    datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                                    "' readonly='readonly' name='transacciones_" +
                                    datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                                    "' style='width:70px; height:65px; text-align:center;' class='form-control' value='" +
                                    datos[i]["Transacciones"] +
                                    "'></td>";
                                html +=
                                    "<td><input inputmode='none' id='btn" +
                                    datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                                    "' style='width:112px; height:60px' type='button' value='Presione Aqui' class='btn btn-primary'    onclick='fn_modalTarjetas(\"" +
                                    datos[i]["fmp_id"] +
                                    '","' +
                                    datos[i]["ctrc_id"] +
                                    '","' +
                                    datos[i]["usr_id"] +
                                    '",' +
                                    datos[i]["arc_valor"] +
                                    "," +
                                    datos[i]["Transacciones"] +
                                    "," +
                                    datos[i]["fpf_total_pagar"] +
                                    "," +
                                    datos[i]["diferencia"] +
                                    "," +
                                    datos[i]["estadoSwitch"] +
                                    ', "' +
                                    datos[i]["fmp_descripcion"] +
                                    '", ' +
                                    datos[i]["fpf_total_pagar"] +
                                    ")'></td>";
                            } else {
                                if (datos[i]["estadoSwitch"] == 1 || datos[i]["estadoSwitch"] == 2) {
                                    html += "<td><input inputmode='none' id='transaccionesIngresadas' readonly='readonly' onkeypress='' style='width:70px; height:65px; text-align:center;' class='form-control' value='0'></td>"; //                                           
                                } else {
                                    html +=
                                        "<td><input inputmode='none' id='transaccionesIngresadas" +
                                        datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                                        "' onkeypress='fn_numeros(event);' name='transacciones_" +
                                        datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                                        "' onclick='fn_borrarcantidadTeclado(\"" +
                                        datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                                        "\");' style='width:70px; height:65px; text-align:center;' class='form-control' value='0'></td>"; //
                                }

                                html +=
                                    "<td><input inputmode='none' id='btn" +
                                    datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                                    "'  style='width:112px; height:65px' type='button' class='btn btn-primary' value='Presione Aqui' onclick='fn_modalTarjetas(\"" +
                                    datos[i]["fmp_id"] +
                                    '","' +
                                    datos[i]["ctrc_id"] +
                                    '","' +
                                    datos[i]["usr_id"] +
                                    '",' +
                                    datos[i]["arc_valor"] +
                                    "," +
                                    datos[i]["Transacciones"] +
                                    "," +
                                    datos[i]["fpf_total_pagar"] +
                                    "," +
                                    datos[i]["diferencia"] +
                                    ", " +
                                    datos[i]["estadoSwitch"] +
                                    ")'></td>";
                            }
                        }

                        html +=
                            "<td><input inputmode='none' readonly='readonly' class='form-control' style='width:95px; text-align:center; height:65px' type='text' type='text' id='posCalculado" +
                            datos[i]["fmp_descripcion"] +
                            "' value='" +
                            datos[i]["fpf_total_pagar"].toFixed(2) +
                            "'></td>";
                        html +=
                            "<td><input inputmode='none' class='form-control' readonly='readonly' style='width:95px;text-align:center; height:65px' type='text' id='diferencia" +
                            datos[i]["fmp_descripcion"] +
                            "' value='" +
                            datos[i]["diferencia"].toFixed(2) +
                            "'></td>";
                        id_botonesArqueoRetiro[i] =
                            "btn" + datos[i]["fmp_descripcion"].replace(/\s/g, "") + ""; //almacena id's de los botones "Presione Aqui"

                        //arqueo
                        $("#tpieEfectivo tr:eq(0) td:eq(0)").css("width", "165px");
                        $("#tpieEfectivo tr:eq(0) td:eq(1)").css("width", "115px");
                        $("#tpieEfectivo tr:eq(0) td:eq(2)").css("width", "115px");
                        $("#tpieEfectivo tr:eq(0) td:eq(3)").css("width", "115px");
                        $("#tpieEfectivo tr:eq(0) td:eq(4)").css("width", "91px");
                        $("#tpieEfectivo tr:eq(0) td:eq(5)").css("width", "91px");
                        $("#tpieEfectivo tr:eq(0) td:eq(6)").css("width", "134px");
                        $("#tpieEfectivo tr:eq(0) td:eq(7)").css("width", "114px");
                        $("#tpieEfectivo tr:eq(0) td:eq(8)").css("width", "115px");
                    } else { //retiro de dinero
                        //***********EGRESOS E INGRESOS*******************//
                        html += "<td><input inputmode='none' id='' readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value='" + TotalEgresos.toFixed(2) + "'></td>";
                        html += "<td><input inputmode='none' id='' readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value='" + TotalIngresos.toFixed(2) + "'></td>";
                        //************************************************//
                        html +=
                            "<td><input inputmode='none' id='retirado_" +
                            datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                            "' readonly='readonly' style='width:105px; height:65px; text-align:center;' class='form-control' value=" +
                            datos[i]["arc_valor"].toFixed(2) +
                            "></td>"; //
                        html +=
                            "<td><input inputmode='none' id='transacciones_" +
                            datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                            "' readonly='readonly' style='width:105px; height:65px; text-align:center;' class='form-control' value=" +
                            datos[i]["Transacciones"] +
                            "></td>"; //

                        if (datos[i]["fmp_descripcion"] == "EFECTIVO") {
                            formaPagoDescripcion = datos[i]["fmp_descripcion"].replace(
                                /\s/g,
                                ""
                            );
                            html +=
                                "<td><input inputmode='none' id='btn" +
                                datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                                "'  style='width:112px; height:65px' type='button' class='btn btn-primary' value='Presione Aqui' onclick='fn_modalBilletesEfectivo(\"" +
                                datos[i]["fmp_id"] +
                                '","' +
                                datos[i]["ctrc_id"] +
                                '","' +
                                datos[i]["usr_id"] +
                                '",' +
                                datos[i]["arc_valor"] +
                                "," +
                                datos[i]["Transacciones"] +
                                "," +
                                datos[i]["fpf_total_pagar"] +
                                "," +
                                datos[i]["diferencia"] +
                                ',"' +
                                formaPagoDescripcion +
                                '",' +
                                transaccionesIngresadas +
                                ", " +
                                datos[i]["estadoSwitch"] +
                                ")'></td>";
                            idformaPago = datos[i]["fmp_id"];
                            totalEfectivo = datos[i]["fpf_total_pagar"];
                            $("#hide_totalPosEfectivo").val(totalEfectivo);
                            estadoSwitch = datos[i]["estadoSwitch"];
                        } else if (
                            datos[i]["fmp_descripcion"] == "CONSUMORECARGA" ||
                            datos[i]["fmp_descripcion"] == "FIDELIZACION" ||
                            datos[i]["fmp_descripcion"] == "VITALITY" ||
                            datos[i]["fmp_descripcion"] == "CUPONEFECTIVO" ||
                            datos[i]["es_agregador"] == "SI"
                        ) {
                            estadoSwitch = 100;
                            datos[i]["estadoSwitch"] = 100;
                            html +=
                                "<td><input inputmode='none' id='btnT" +
                                datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                                "' style='width:112px; height:60px' type='button' value='Presione Aqui' class='btn btn-primary'    onclick='fn_modalTarjetas(\"" +
                                datos[i]["fmp_id"] +
                                '","' +
                                datos[i]["ctrc_id"] +
                                '","' +
                                datos[i]["usr_id"] +
                                '",' +
                                datos[i]["arc_valor"] +
                                "," +
                                datos[i]["Transacciones"] +
                                "," +
                                datos[i]["fpf_total_pagar"] +
                                "," +
                                datos[i]["diferencia"] +
                                "," +
                                datos[i]["estadoSwitch"] +
                                ', "' +
                                datos[i]["fmp_descripcion"] +
                                '", ' +
                                datos[i]["fpf_total_pagar"] +
                                ")'></td>";
                        } else {
                            if (datos[i]["es_agregador"] == "SI") {
                                estadoSwitch = 100;
                                datos[i]["estadoSwitch"] = 100;

                                html +=
                                    "<td><input inputmode='none' id='btnT" +
                                    datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                                    "' style='width:112px; height:60px' type='button' value='Presione Aqui' class='btn btn-primary'    onclick='fn_modalTarjetas(\"" +
                                    datos[i]["fmp_id"] +
                                    '","' +
                                    datos[i]["ctrc_id"] +
                                    '","' +
                                    datos[i]["usr_id"] +
                                    '",' +
                                    datos[i]["arc_valor"] +
                                    "," +
                                    datos[i]["Transacciones"] +
                                    "," +
                                    datos[i]["fpf_total_pagar"] +
                                    "," +
                                    datos[i]["diferencia"] +
                                    "," +
                                    datos[i]["estadoSwitch"] +
                                    ', "' +
                                    datos[i]["fmp_descripcion"] +
                                    '", ' +
                                    datos[i]["fpf_total_pagar"] +
                                    ")'></td>";
                            } else {
                                html +=
                                    "<td><input inputmode='none' id='btn" +
                                    datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                                    "'  style='width:112px; height:65px' type='button' class='btn btn-primary' value='Presione Aqui' onclick='fn_modalTarjetas(\"" +
                                    datos[i]["fmp_id"] +
                                    '","' +
                                    datos[i]["ctrc_id"] +
                                    '","' +
                                    datos[i]["usr_id"] +
                                    '",' +
                                    datos[i]["arc_valor"] +
                                    "," +
                                    datos[i]["Transacciones"] +
                                    "," +
                                    datos[i]["fpf_total_pagar"] +
                                    "," +
                                    datos[i]["diferencia"] +
                                    ", " +
                                    datos[i]["estadoSwitch"] +
                                    ")'></td>";
                            }
                        }

                        html +=
                            "<td><input readonly='readonly' class='form-control' style='width:105px; text-align:center; height:65px' type='text' type='text' id='posCalculado" +
                            datos[i]["fmp_descripcion"] +
                            "' value='" +
                            datos[i]["fpf_total_pagar"].toFixed(2) +
                            "'></td>";
                        html +=
                            "<td><input class='form-control' readonly='readonly' style='width:105px;text-align:center; height:65px' type='text' id='diferencia" +
                            datos[i]["fmp_descripcion"] +
                            "' value='" +
                            datos[i]["diferencia"].toFixed(2) +
                            "'></td>";
                        id_botonesRetiro[i] =
                            "btn" + datos[i]["fmp_descripcion"].replace(/\s/g, "") + ""; //almacena id's de los botones "Presione Aqui"
                        //retiro
                        $("#tpieEfectivo tr:eq(0) td:eq(0)").css("width", "159px");
                        $("#tpieEfectivo tr:eq(0) td:eq(1)").css("width", "119px");
                        $("#tpieEfectivo tr:eq(0) td:eq(2)").css("width", "119px");
                        $("#tpieEfectivo tr:eq(0) td:eq(3)").css("width", "119px");
                        $("#tpieEfectivo tr:eq(0) td:eq(4)").css("width", "119px");
                        $("#tpieEfectivo tr:eq(0) td:eq(6)").css("width", "127px");
                        $("#tpieEfectivo tr:eq(0) td:eq(7)").css("width", "120px");
                        $("#tpieEfectivo tr:eq(0) td:eq(8)").css("width", "120px");
                    }
                    html +=
                        "<input type='hidden' id='id_" +
                        datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                        "' value=" +
                        datos[i]["fmp_id"] +
                        "></tr>";

                    $("#totalPosEfectivo").val("");
                    $("#tpie_EfectivoBilletes tr:eq(1) td:eq(1)").html("");
                    $("#totalPosEfectivo").val(totalEfectivo.toFixed(2));
                    $("#tpie_EfectivoBilletes tr:eq(1) td:eq(1)").html(totalEfectivo.toFixed(2));

                    idformaPago = datos[i]["fmp_id"];
                    idcontrolEstacion = datos[i]["ctrc_id"];
                    $("#hid_controlEstacion").val(datos[i]["ctrc_id"]);
                    $("#formaPagoEfectivo").html(html);
                }

                fn_tecladoTransacciones(arrayArqueo);
                fn_calculaValoresArqueo(arrayArqueo, numeroFormasPago);
                fn_valorTotalFormasPago(idcontrolEstacion, idformaPago);
                fn_TarjetasDatafast(1);
                $("#modal_formaPagoEfectivo").modal("show");
                //////////////////////////BOTON CANCELAR/////////////////////////////////////////////////////////////////////////		
                $("#btn_cancelarEfectivo").click(function() {
                    $("#modal_formaPagoEfectivo").modal("hide");
                    $("#hid_controlRetiroEfectivo").val(0);
                    $("#tpieEfectivo tr:eq(0) td:eq(3)").html('');
                });
            } else {
                fn_TarjetasDatafast(0);
            }
        }
    });
}

function fn_tecladoTransacciones(arrayArqueo) {
    for (var i = 0; i < arrayArqueo.length; i++) {
        $("#transaccionesIngresadas" + arrayArqueo[i] + "").keypad(); //teclado virtual
        $("#arrayArqueoCuadreTarjetas" + arrayArqueo[i] + "").keypad(); //teclado virtual
    }
}

function fn_borrarcantidadTeclado(arrayTr) {
    $('input:text[name="transacciones_' + arrayTr + '"]').val("");
}

function fn_borrarcantidadTecladoMontoActual(arrayArqueo, descripcion) {
    $('input:text[name="montoActual_' + arrayArqueo + '"]').val("");
}

/*************************************************************************/
/*Funcion que trae la suma total de valores de Formas de Pago            */
/*************************************************************************/
function fn_valorTotalFormasPago(codigo_ctrEstacion, idformaPago) {
    var send;
    send = { consultatotalEstacion: 1 };
    send.accion = 1;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                //valores retiro de dinero
                $("#tpieEfectivo tr:eq(0) td:eq(1)").html(
                    "$" + datos.TotalEgresos.toFixed(2)
                );
                $("#tpieEfectivo tr:eq(0) td:eq(2)").html(
                    "$" + datos.TotalIngresos.toFixed(2)
                );
                $("#tpieEfectivo tr:eq(0) td:eq(3)").html(
                    "$" + datos.totalRetirosFormaPagos.toFixed(2)
                );
                $("#tpieEfectivo tr:eq(0) td:eq(4)").html(datos.transacciones);
                $("#tpieEfectivo tr:eq(0) td:eq(7)").html("$" + datos.total.toFixed(2));
                $("#tpieEfectivo tr:eq(0) td:eq(8)").html(
                    "$" + datos.totalDiferenciaFormaPagos.toFixed(2)
                );

                //valores desasignar cajero
                $("#tpie tr:eq(0) td:eq(3)").html(
                    "$" + datos.totalRetirosFormaPagos.toFixed(2)
                );
                $("#tpie tr:eq(0) td:eq(7)").html(
                    "$" + datos.totalDiferenciaFormaPagos.toFixed(2)
                );
                $("#tpie tr:eq(0) td:eq(4)").html(datos.transacciones);
                $("#tpie tr:eq(0) td:eq(1)").html("$" + datos.TotalEgresos.toFixed(2));
                $("#tpie tr:eq(0) td:eq(2)").html("$" + datos.TotalIngresos.toFixed(2));

                totalEstacion = datos.total;
            }
        }
    });
}

function fn_ejecutaBotonOkEfectivo() {
    var send;
    valor_retiro_efectivo = $("#hide_totalBilletesEfectivo").val();
    if (banderaDesmontado == 2) { //validacion para desmontado
        for (var i = 0; i < id_botones.length; i++) {
            if (isNaN($("#" + id_botones[i] + "").val())) {
                alertify.alert("Faltan val&oacute;res por ingresar ó cuadre en cero los valores.");
                return false;
            }
        }
    }

    if (banderaDesmontado == 1) {
        alertify.confirm("Confirma Retiro de valores?");
    } else if (banderaDesmontado == 3) {
        alertify.confirm("Confirma Arqueo de valores?");
    }

    $("#alertify-ok").click(function() {
        efectivo_posCalculado = $("#hide_totalPosEfectivo").val();
        if (valor_retiro_efectivo == "") {
            valor_retiro_efectivo = 0;
        }

        send = { asentarRetiroEfectivo: 1 };
        send.accion = "u";
        send.estado_asentado_refectivo = 1;

        if (banderaDesmontado == 3) {
            send.efectivo_posCalculado = totalEstacion;
            send.valor_retiro_efectivo = totalEstacion;
        } else {
            send.efectivo_posCalculado = efectivo_posCalculado;
            send.valor_retiro_efectivo = valor_retiro_efectivo;
        }

        send.estadoRetiro = banderaDesmontado;

        var apiImpresion = getConfiguracionesApiImpresion();
        // console.log(apiImpresion);        
        if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
            fn_cargando(1);          
            var result = new apiServicioImpresion('retiros',null,null, send);
            var imprime = result["imprime"];
            var mensaje = result["mensaje"];

            console.log('imprime: ', imprime);
            console.log('mensaje: ', mensaje);

            if(banderaDesmontado == 1){
                if (!imprime) {
                    alertify.success('Imprimiendo Retiros...');
                    fn_cargando(0);
                    $("#hid_controlRetiroEfectivo").val(0);
                    $("#modal_formaPagoEfectivo").modal("hide");
                    $("#hide_totalBilletesEfectivo").val("");  
    
                } else {
                    alertify.alert(mensaje);
                    alertify.success('Error al imprimir Retiros...');
                    $("#hid_controlRetiroEfectivo").val(0);
                    $("#modal_formaPagoEfectivo").modal("hide");
                    $("#hide_totalBilletesEfectivo").val("");  
                    fn_cargando(0);
                }
            }
            
            if (banderaDesmontado == 3) {
                fn_obtieneUsuarioAdministrador();
            }
            

        }else{
            $.ajax({
                async: false,
                type: "GET",
                dataType: "json",
                contentType: "application/x-www-form-urlencoded",
                url: "config_desmontadoCajero.php",
                data: send,
                success: function(datos) {
                    valor_retiro_efectivo = $("#hide_totalBilletesEfectivo").val();
                    if (valor_retiro_efectivo == "") {
                        valor_retiro_efectivo = 0;
                    }
                    send = { auditoriaRetiroEfectivo: 1 };
                    send.accion = "i";
                    if (banderaDesmontado == 3) {
                        send.valor_retiro_efectivo = totalEstacion;
                    } else {
                        send.valor_retiro_efectivo = valor_retiro_efectivo;
                    }
                    $.ajax({
                        async: false,
                        type: "GET",
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded",
                        url: "config_desmontadoCajero.php",
                        data: send,
                        success: function(datos) {
                            $("#hid_controlRetiroEfectivo").val(0);
                            $("#modal_formaPagoEfectivo").modal("hide");
                            $("#hide_totalBilletesEfectivo").val("");
                        }
                    });

                    if (banderaDesmontado == 3) {
                        fn_obtieneUsuarioAdministrador();
                    }
                }
            });
        }
    });
}

function fn_modalBilletesEfectivo(codigo_formaPago, codigo_ctrEstacion, id_usuario, totalRetirado, transacciones, posCalculadoValor, diferencia, formaPagoDescripcion, transaccionesIngresadas) {

    fn_apiAperturaCajon(codigo_formaPago)

    $("#totalRetirado").val(totalRetirado);
    $("#transacciones").val(transacciones);
    $("#posCalculadoValor").val(posCalculadoValor);
    $("#diferencia").val(diferencia);
    $("#hid_formaPago").val(codigo_formaPago);
    $("#formaPagoDescripcion").val(formaPagoDescripcion);
    $("#transaccionesIngresadas").val(transaccionesIngresadas);
    controlaRetiroEfectivo = $("#hid_controlRetiroEfectivo").val(); //controla si va a ingresar nueva cantidad de billetes o ya los ingreso y va a modificar
    $("#tpie_EfectivoBilletes tr:eq(2) td:eq(1)").html(totalRetirado.toFixed(2));
    $("#tpie_EfectivoBilletes tr:eq(3) td:eq(1)").html(posCalculadoValor.toFixed(2));
    $("#tpie_EfectivoBilletes tr:eq(1) td:eq(1)").html(diferencia.toFixed(2));
    if (controlaRetiroEfectivo == 0) {
        $("#totalNuevoEfectivo").val("");
        $("#tpie_EfectivoBilletes tr:eq(0) td:eq(1)").html("");

        codigo_formaPago = codigo_formaPago; //codigo id de la forma de pago efectivo	
        send = { consultaBilletes: 3 };
        send.accion = 3;
        $.ajax({
            async: false,
            type: "GET",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_desmontadoCajero.php",
            data: send,
            success: function(datos) {
                var html = "<thead><tr class='active'><th width='266px' colspan='2' style='text-align:center' class='tituloEtiqueta'>Denominaciones</th><th width='266px' class='tituloEtiqueta' style='text-align:center'>Cantidad</th><th width='266px' class='tituloEtiqueta' style='text-align:center'>Total</th></thead>";
                if (datos.str > 0) {
                    $("#billetes").empty();
                    miarray = new Array(datos.str);
                    for (var i = 0; i < datos.str; i++) {
                        if ($.trim(datos[i]["btd_Tipo"]) == "BILLETE") {
                            miarray[i] = "billete" + datos[i]["btd_Valor"] + "";
                            miarray[i] = miarray[i].replace(/\./g, "");
                            html +=
                                "<tr><td align='center'><img src='../imagenes/billete.png'/></td><td><input inputmode='none' class='form-control' width='266px' align='center'  style='height:50px; text-align:center' readonly='readonly' type='text' id=" +
                                miarray[i] +
                                " value=" +
                                datos[i]["btd_Valor"] +
                                "></td>";
                        } else {
                            miarray[i] = "moneda" + datos[i]["btd_Valor"] + "";
                            miarray[i] = miarray[i].replace(/\./g, "");
                            html +=
                                "<tr><td align='center'><img src='../imagenes/moneda.png'/></td><td><input inputmode='none' class='form-control' width='266px' align='center'  style='height:50px; text-align:center' readonly='readonly' type='text' id=" +
                                miarray[i] +
                                " value=" +
                                datos[i]["btd_Valor"] +
                                "></td>";
                        }
                        html +=
                            "<td><input inputmode='none' class='form-control' name=" +
                            datos[i]["btd_id"] +
                            " align='center' width='266px' onkeypress='fn_numeros(event);' maxlength='7' onclick='fn_borrarcantidad(\"" +
                            datos[i]["btd_id"] +
                            "\");' id='b" +
                            miarray[i] +
                            "' align='right' style='height:50px; text-align:center' type='text' value=0></td>";
                        html += "<td><input inputmode='none' class='form-control' align='center' width='266px' id='t" + miarray[i] + "' align='right' style='height:50px; text-align:center' type='text' readonly='readonly' value=0></td>";

                        html +=
                            "<input inputmode='none' type='hidden' id='h" +
                            miarray[i] +
                            "' value=" +
                            datos[i]["btd_id"] +
                            ">";
                        html += "<input inputmode='none' type='hidden' id='h2" + miarray[i] + "' value=" + codigo_ctrEstacion + ">";
                        html += "<input inputmode='none' type='hidden' id='h3" + miarray[i] + "' value=" + id_usuario + "></tr>";
                        $("#billetesEfectivo").html(html);
                    }
                    fn_calculaSubtotalesEfectivo(miarray, codigo_formaPago, id_usuario);
                }
            }
        });
    } else {
        if (banderaDesmontado === 1) { //Retiros
            accionModificaBilletes = 4;
        } else if (banderaDesmontado === 3) { //Arqueo
            accionModificaBilletes = 5;
        }
        var html = "<tr class='active'><th colspan='2' style='text-align:center'>Denominaciones</th><th style='text-align:center'>Cantidad</th><th style='text-align:center;'>Total</th></tr>";
        send = { consultaBilletesModificados: 1 };
        send.accion = accionModificaBilletes;
        $.ajax({
            async: false,
            type: "GET",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_desmontadoCajero.php",
            data: send,
            success: function(datos) {
                if (datos.str > 0) {
                    miarray = new Array(datos.str);
                    $("#billetes").empty();
                    for (var i = 0; i < datos.str; i++) {
                        if ($.trim(datos[i]["btd_Tipo"]) == "BILLETE") {
                            miarray[i] = "billete" + datos[i]["btd_Valor"] + "";
                            miarray[i] = miarray[i].replace(/\./g, "");
                            html +=
                                "<tr><td align='center'><img src='../imagenes/billete.png'/></td><td><input inputmode='none' class='form-control' width='266px' align='center'  style='height:50px; text-align:center' readonly='readonly' type='text' id=" +
                                miarray[i] +
                                " value=" +
                                datos[i]["btd_Valor"] +
                                "></td>";
                        } else {
                            miarray[i] = "moneda" + datos[i]["btd_Valor"] + "";
                            miarray[i] = miarray[i].replace(/\./g, "");
                            html +=
                                "<tr><td align='center'><img src='../imagenes/moneda.png'/></td><td><input inputmode='none' class='form-control' width='266px' align='center'  style='height:50px; text-align:center' readonly='readonly' type='text' id=" +
                                miarray[i] +
                                " value=" +
                                datos[i]["btd_Valor"] +
                                "></td>";
                        }
                        html +=
                            "<td><input inputmode='none' class='form-control' name =" +
                            datos[i]["btd_id"] +
                            " align='center' width='266px' onkeypress='fn_numeros(event);' maxlength='7' onclick='fn_borrarcantidad(\"" +
                            datos[i]["btd_id"] +
                            "\");' id='b" +
                            miarray[i] +
                            "' align='right' style='height:50px; text-align:center' type='text' value=" +
                            datos[i]["bte_cantidad"] +
                            "></td>";
                        html +=
                            "<td><input inputmode='none' class='form-control' align='center' width='266px' id='t" +
                            miarray[i] +
                            "' align='right' style='height:50px; text-align:center' type='text' readonly='readonly' value=" +
                            datos[i]["bte_total"] +
                            "></td>";

                        html +=
                            "<input inputmode='none' type='hidden' id='h" +
                            miarray[i] +
                            "' value=" +
                            datos[i]["btd_id"] +
                            ">";
                        html += "<input inputmode='none' type='hidden' id='h2" + miarray[i] + "' value=" + codigo_ctrEstacion + ">";
                        html += "<input inputmode='none' type='hidden' id='h3" + miarray[i] + "' value=" + id_usuario + "></tr>";
                        $("#billetesEfectivo").html(html);
                    }
                    fn_calculaSubtotalesEfectivo(miarray, codigo_formaPago, id_usuario);
                    $("#tpie_EfectivoBilletes tr:eq(1) td:eq(1)").html($("#hid_diferencia").val());
                }
            }
        });
    }
}

function fn_borrarcantidad(array) {
    $("input:text[name=" + array + "]").val("");
}

function fn_calculaSubtotalesEfectivo(array, codigo_formaPago, idUsuario) {
    $("#array").val(array);
    $("#hid_usuario_efectivo").val(idUsuario);
    for (var i = 0; i < array.length; i++) {
        $("#b" + array[i] + "").keypad(); //teclado virtual			
    }

    for (var i = 0; i < subtotales.length; i++) {
        subtotales[i] = $("#t" + array[i] + "").val();
    }

    for (var i = 0; i < ocultos.length; i++) {
        ocultos[i] = $("#h" + array[i] + "").val();
    }

    for (var i = 0; i < ocultos2.length; i++) {
        ocultos2[i] = $("#h2" + array[i] + "").val();
    }

    for (var i = 0; i < ocultos3.length; i++) {
        ocultos3[i] = $("#h3" + array[i] + "").val();
    }

    $("#ModalBilletesEfectivo").modal("show");
    $("#cancelarEfectivo")
        .click(function() {
            $("#hide_totalBilletesEfectivo").val("");
            $("#ModalBilletesEfectivo").modal("hide");
        })
        .css({});

    for (var i = 0; i < array.length; i++) { //inicio del FOR		
        $("#b" + array[i] + "").focus(function() { //inicio evento focus
            for (var i = 0; i < denominaciones.length; i++) {
                denominaciones[i] = $("#" + array[i] + "").val();
            }

            for (var i = 0; i < cantidades.length; i++) {
                if ($("#b" + array[i] + "").val() == 0) {
                    $("#b" + array[i] + "").val(0);
                }
                cantidades[i] = $("#b" + array[i] + "").val();
            }

            for (var i = 0; i < resultado.length; i++) {
                resultado[i] = denominaciones[i] * cantidades[i];
            }

            suma = 0;
            for (var i = 0; i < array.length; i++) {
                $("#t" + array[i] + "").val(resultado[i].toFixed(2));
                suma += parseFloat(resultado[i]);
            }

            suma2 = suma; //totalNuevo
            $("#totalNuevoEfectivo").val("$" + suma2.toFixed(2));
            $("#tpie_EfectivoBilletes tr:eq(0) td:eq(1)").html(suma2.toFixed(2));

            $("#hide_totalBilletesEfectivo").val("");
            $("#hide_totalBilletesEfectivo").val(suma2.toFixed(2));

            dif = $("#diferencia").val();

            dif_Total = parseFloat(dif) + parseFloat(suma2);
            $("#hid_masomenos").val(dif_Total.toFixed(2));
            $("#tpie_EfectivoBilletes tr:eq(1) td:eq(1)").html(dif_Total.toFixed(2));

            if (dif_Total == 0) {
                $("#tr_masomenos").addClass("success");
                $("#tr_masomenos").removeClass("danger");
            } else {
                $("#tr_masomenos").addClass("danger");
                $("#tr_masomenos").removeClass("success");
            }

        }); //fin evento focus

        valorsumabilletes = $("#valorsumabilletes").val();
        valormasomenos = $("#valormasomenos").val();
        input_dif_Total = $("#hid_masomenos").val();

        if (parseFloat(input_dif_Total) == 0) {
            $("#tr_masomenos").addClass("success");
            $("#tr_masomenos").removeClass("danger");
        } else {
            $("#tr_masomenos").addClass("danger");
            $("#tr_masomenos").removeClass("success");
        }
    }
} // fin de la funcion

function fn_guardarTotalBilletesEfectivo() {
    //validacion para que no se retire mas dinero que el claculado por el sistema.
    if (banderaDesmontado == 1) {
        var valorRetiradoEfectivoRetiros = $("#td_masomenosRetiros").text();
        if (parseFloat(valorRetiradoEfectivoRetiros) > parseFloat(0)) {
            alertify.alert("La cantidad que intenta retirar es mayor a la calculada por el Sistema.");
            return false;
        }
    }

    resultado = $.grep(resultado, function(n) {
        return n == 0 || n;
    }); //eliminar posiciones vacias de un array//cp
    codigo_formaPago = $("#hid_formaPago").val();
    idUsuario = $("#hid_usuario_efectivo").val();

    if ($("#totalNuevoEfectivo").val() == "") {
        alertify.alert("Ingrese cantidades");
        return false;
    }

    $("#hid_controlRetiroEfectivo").val(1);

    totalNuevo = $("#totalNuevoEfectivo").val();
    send = { grabaBilletes: 1 };
    send.accion = "I";
    send.cantidades2 = cantidades;
    send.resultado2 = resultado;
    send.oculto = ocultos;
    send.oculto2 = ocultos2;
    send.oculto3 = ocultos3;
    send.tipoEfectivo = 2;
    send.banderaDesmontado = banderaDesmontado;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            ////////////////////////////////ULTIMO AGREGADO///////////////////////////////////////
            array = $("#array").val();
            send = { consultaidBilletes: 1 };
            send.accion = 2;
            send.top = array.length;
            $.ajax({
                async: false,
                type: "POST",
                dataType: "json",
                contentType: "application/x-www-form-urlencoded",
                url: "config_desmontadoCajero.php",
                data: send,
                success: function(datos) {
                    arrayBilletes = new Array(datos.str);
                    for (var i = 0; i < datos.str; i++) {
                        arrayBilletes[i] = "" + datos[i]["bte_id"] + "";
                    }
                    arrayBilletes2 = arrayBilletes;
                }
            });
            /////////////////////////////FIN ULTIMO AGREGADO////////////////////////////////////////
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });

    totalRetiradoA = $("#totalRetirado").val(); //retirado
    transacciones = $("#transacciones").val(); //transacciones
    posCalculadoValor = $("#posCalculadoValor").val(); //posCalculado
    totalBilletes = parseFloat(
        $("#tpie_EfectivoBilletes tr:eq(0) td:eq(1)").text()
    );
    diferenciaA = $("#tpie_EfectivoBilletes tr:eq(0) td:eq(1)").text();

    if (diferenciaA == 0) {
        diferencia = totalBilletes - posCalculadoValor;
    } else {
        diferencia = parseFloat(diferenciaA) + parseFloat(totalBilletes);
    }

    idUsuario = $("#hid_usuario_efectivo").val();

    if (totalRetiradoA == 0) {
        totalRetirado = totalBilletes;
    } else {
        totalRetirado = parseFloat($("#totalRetirado").val()) + parseFloat(totalBilletes);
    }
    formaPagoDescripcion = $("#formaPagoDescripcion").val();
    transaccionesIngresadas = $("#transaccionesIngresadas").val();
    diferenciaB = $("#tpie_EfectivoBilletes tr:eq(1) td:eq(1)").text();
    $("#hid_diferencia").val(diferenciaB);
    send = { grabaArqueo: 1 };
    send.accion = "I";
    send.accion_int = 1;
    send.resNuevo = totalRetirado;
    send.formaPago = codigo_formaPago;
    send.idUsuario = idUsuario;
    send.totalRetirado = totalBilletes;
    send.transacciones = transacciones;
    send.posCalculadoValor = posCalculadoValor;
    send.diferencia = diferencia;
    send.estadoRetiro = banderaDesmontado; //1 cuando es retiro de dinero
    send.estadoPendiente = 3; //cuando el retiro de dinero esta pendiente
    send.arqueoTransacciones = transaccionesIngresadas;
    send.estadoSwitch = -1;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            $("#hid_controlRetiroEfectivo").val(1);
            send = { consultaformaPagoModificado: 1 };
            send.accion = 2;
            send.id_usuariO = idUsuario;
            send.formaPago = codigo_formaPago;
            send.estadoRetiro = banderaDesmontado; //1 cuando es retiro de dinero
            $.ajax({
                async: false,
                type: "GET",
                dataType: "json",
                contentType: "application/x-www-form-urlencoded",
                url: "config_desmontadoCajero.php",
                data: send,
                success: function(datos) {
                    if (datos.str > 0) {
                        $("#diferencia" + datos.fmp_descripcion + "").empty();
                        for (var i = 0; i < datos.str; i++) {
                            $("#diferencia" + datos[i]["fmp_descripcion"] + "").val(
                                datos[i]["diferencia"].toFixed(2)
                            );
                            if (banderaDesmontado == 3) {
                                $("#tpieEfectivo tr:eq(0) td:eq(3)").html(
                                    datos[i]["transaccionesIngresadas"]
                                );
                            }
                        }
                    }
                    totalBilletes = $("#hide_totalBilletesEfectivo").val();
                    fn_calculatotalesModificados(idUsuario);
                    fn_totalesIngresadosEfectivo(idUsuario, totalBilletes);
                }
            });
            $("#ModalBilletesEfectivo").modal("hide");
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

function fn_eliminarBilletesPendiente() {
    var send;
    $("#hide_totalBilletesEfectivo").val("");
    send = { eliminaBilletesPendiente: 1 };
    send.accion = "e";
    send.estado_pendiente_efectivo = 2;
    send.estadoRetiro = banderaDesmontado;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {}
        }
    });
}

function fn_desasignarEnEstacion() {
    var send;
    send = { desasignarEnEstacion: 1 };
    send.accion = 1;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                if (datos.desasignaEstacion == 1) {
                    $("#btn_desasignarDirecto").hide();
                    $("#btn_corteCaja").show();
                } else {
                    $("#btn_desasignarDirecto").show();
                    $("#btn_corteCaja").hide();
                }
            } else {
                alertify.alert("Estaci\u00F3n no configurada para desasignar el cajero en Punto de Venta \u00F3 Administraci\u00F3n");
                $("#btn_retiroEfectivo").attr("disabled", true);
                $("#btn_corteCaja").attr("disabled", true);
                $("#btn_desasignarDirecto").attr("disabled", true);
                $("#btn_impresionCorteX").attr("disabled", true);
                $("#btn_arqueoRetiroEfectivo").attr("disabled", true);

            }
        }
    });
}

function fn_botonesDinamicosTecladoDescuadre() {
    var send;
    send = { traeMotivosDescuadre: 1 };
    send.accion = 1;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                $("#motivos_descuadre1").empty();
                for (var i = 0; i < datos.str; i++) {
                    html = "<div class='col-xs-3'><button class='btnVirtualSugerencias' style = 'width:185px; height:80px' onclick='fn_borrarTextArea(); fn_agregarCaracter(txtArea,\"" + datos[i]["mtv_descripcion"] + "\")'>" + datos[i]["mtv_descripcion"] + "</button></div>";
                    $("#motivos_descuadre1").append(html);
                }
            } else {
                alertify.alert("Motivos de Descuadre no configurados");
            }
        }
    });
}

function fn_borrarTextArea() {
    $("#txtArea").val("");
}

function fn_limpiarCantidad() {
    $("#txt_montoTarjeta").val("");
    coma = 0;
}

function fn_validaAdmin() {
    var send;
    if ($("#usr_claveAdmin").val() == "") {
        $("#usr_claveAdmin").focus();
        alertify.alert("Ingrese una clave.");
        return false;
    }
    send = { validarUsuarioAdministrador: 1 };
    send.usr_Admin = $("#usr_claveAdmin").val();
    $.getJSON("config_desmontadoCajero.php", send, function(datos) {
        if (datos.admini == 1) {
            if (lc_banderaOkAdmin == "formaPago") {
                $("#Modal_credencialesAdmin").modal("hide");
                $("#modal_agregarFormaPago").modal("show");
                send = { cargarFormasPago: 1 };
                send.accion = 1;
                $.ajax({
                    async: false,
                    type: "GET",
                    dataType: "json",
                    contentType: "application/x-www-form-urlencoded",
                    url: "config_desmontadoCajero.php",
                    data: send,
                    success: function(datos) {
                        if (datos.str > 0) {
                            $("#sel_formasPago").html("");
                            $("#sel_formasPago").html(
                                "<option selected value='0'>---Seleccione Forma de Pago---</option>"
                            );
                            for (var i = 0; i < datos.str; i++) {
                                html =
                                    "<option value='" +
                                    datos[i]["fmp_id"] +
                                    "' name ='" +
                                    datos[i]["fmp_descripcion"] +
                                    "' >" +
                                    datos[i]["fmp_descripcion"] +
                                    "</option>";
                                $("#sel_formasPago").append(html);
                            }
                            $("#sel_formasPago").change(function() {
                                fmp_id = $("#sel_formasPago").val();
                                fmp_descripcion = $(this)
                                    .find("option:selected")
                                    .text();
                                $("#id_formaPago").val(fmp_id);
                                $("#hide_fmp_descripcion").val(fmp_descripcion);

                            });
                        }
                    }
                });
            }
        } else {
            alertify.set({ labels: { ok: "OK" } });
            alertify.alert("Clave incorrecta.");
            $("#usr_claveAdmin").val("");
            return false;
        }
    });
}

function fn_modal_credencialesAdmin() {
    $("#txt_montoTarjeta").removeAttr("disabled");
    $(".btnVirtualBorrarTarjetas").removeAttr("disabled");
    $(".btnVirtual").removeAttr("disabled");
    $("#usr_claveAdmin").val("");
    alertify.set({ labels: { ok: "SI", cancel: "NO" } });
    lc_banderaOkAdmin = "formaPago";
    $("#credencialesAdmin").show();
    $("#Modal_credencialesAdmin").modal("show");
}

function fn_cerrarValidaAdmin() {
    $("#Modal_credencialesAdmin").modal("hide");
    $("#usr_claveAdmin").val("");
}

function fn_agregarFormaPago() {
    if ($("#sel_formasPago").val() == 0) {
        alertify.set({ labels: { ok: "OK" } });
        alertify.alert("Seleccione una Forma de Pago");
        return false;
    }

    fmp_descripcion = $("#hide_fmp_descripcion").val();
    fmp_id = $("#id_formaPago").val();
    send = { grabaarqueoformapago: 1 };
    totalFormaPago = 0;
    send.accion = "I";
    send.accion_int = 2;
    send.fmp_id = fmp_id;
    send.totalFormaPago = totalFormaPago;
    send.banderafp = 1;
    send.estadoRetiro = banderaDesmontado;
    send.estadoSwitch = estadoSwitch;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            html = "";
            html += "<tr><td width='180' align='center' style='vertical-align:middle'>" + fmp_descripcion + "</td>";
            //***********EGRESOS E INGRESOS*******************//
            html += "<td><input id='' readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value='0.00'></td>";
            html += "<td><input id='' readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value='0.00'></td>";
            //************************************************//
            html += "<td><input readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value='0.00'></td>";
            html += "<td style='text-align:center;'><input readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value='1'></td>";
            html +=
                "<td><input inputmode='none' id='btnT" +
                fmp_descripcion.replace(/\s/g, "") +
                "' style='width:112px; height:60px' type='button' value='Presione Aqui' class='btn btn-primary'	onclick='fn_modalTecladoFormasPago(\"" +
                fmp_descripcion +
                '","' +
                fmp_id +
                "\")'></td>";
            html += "<td><input readonly='readonly' class='form-control' style='width:105px; text-align:center; height:60px' type='text' type='text' id='inputT" + fmp_descripcion.replace(/\s/g, "") + "' value=''></td>";
            html += "<td><input inputmode='none' class='form-control' readonly='readonly' style='width:105px;text-align:center; height:60px' type='text' id='diferenciat" + fmp_descripcion.replace(/\s/g, "") + "' value = '' ></td>";

            $("#formaPago").append(html);
            numeroFormasPago++;
            id_botones.push("btnT" + fmp_descripcion.replace(/\s/g, ""));
        }
    });
    $("#modal_agregarFormaPago").modal("hide");
}

function fn_modalTecladoFormasPago(fmp_descripcion, fmp_id) {
    var send;
    $("#tituloModalTecladoNuevaFormaPago").html(
        "Corte en Z - " + fmp_descripcion
    );
    send = { traeValorFormaPago: 1 };
    send.accion = 2;
    send.fmp_id = fmp_id;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            $("#tecladoNuevaFormaPago").empty();
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html = "<table align='center'><tr></tr>";
                    html += "<tr><th>Ingrese Monto: " + fmp_descripcion + "</th></tr>";
                    if (datos[i]["arc_valor"] == "0") {
                        html += "<tr><td><input inputmode='none' id='txt_montoFormaPago' onkeypress='return fn_numeros(event)'  style='text-align:center; font-size:30px' type='text' value=''></td></tr>";
                    } else {
                        html +=
                            "<tr><td><input inputmode='none' id='txt_montoFormaPago' onkeypress='return fn_numeros(event)'  style='text-align:center; font-size:30px' type='text' value='" +
                            datos[i]["arc_valor"].toFixed(2) +
                            "'></td></tr>";
                    }
                    html += "</table></br>";
                    html += "<table align='center'><tr><td><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumerof('7') >7</button></td><td><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumerof('8')>8</button></td><td><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumerof('9')>9</button></td></tr><tr><td><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumerof('4')>4</button></td><td><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumerof('5')>5</button></td><td><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumerof('6')>6</button></td></tr><tr><td><button class='btnVirtual'  style='font-size: 34px;' onclick=fn_agregarNumerof('1')>1</button></td><td><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumerof('2')>2</button></td><td><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumerof('3')>3</button></td></tr><tr><td ><button class='btnVirtual' style='font-size: 34px;' onclick=fn_agregarNumerof('0')>0</button></td><td><button class='btnVirtualBorrarTarjetas' style='font-size: 34px;' id='btn_punto' onclick=fn_agregarNumerof('.')>.</button></td><td><button class='btnVirtualBorrarTarjetas' style='font-size: 34px;' id='btn_borrar' onclick=fn_eliminarCantidadf()>&larr;</button></td></tr><tr><td colspan='3'><button class='btnVirtualLimpiar' style='font-size: 34px;' id='btn_limpiar' onclick='fn_limpiarCantidadf()'>LIMPIAR</button></td></tr></table><br/>";
                    html +=
                        "<div class=''><div align='center'><button type='button' id='btn_okTarjeta" +
                        fmp_descripcion +
                        "' class='btn btn-primary ' style='height:65px;width:140px; font-size: 30px;' onclick='fn_guardarFormaPago(\"" +
                        fmp_id +
                        '","' +
                        fmp_descripcion +
                        "\");'>OK</button><button type='button' class='btn btn-default' id='btn_cancelTarjeta' style='height:65px;width:140px; font-size: 30px;' onclick='' data-dismiss='modal' value='Cancelar'>Cancelar</button></div></div>";
                    $("#tecladoNuevaFormaPago").append(html);
                    $("#ModalTecladoNuevaFormaPago").modal("show");
                }
            }
        }
    });
}

function fn_guardarFormaPago(fmp_id, fmp_descripcion) {
    if ($("#txt_montoFormaPago").val() == "") {
        alertify.alert("Ingrese una cantidad");
        return false;
    }

    if ($("#txt_montoFormaPago").val() == "0.00") {
        alertify.alert("Ingrese una cantidad mayor de cero");
        return false;
    }

    if (isNaN($("#txt_montoFormaPago").val())) {
        alertify.alert("Ingrese monto válido");
        return false;
    }

    banderaFormaPagoCantidad = 1;
    send = { grabaarqueoformapago: 1 };
    totalFormaPago = $("#txt_montoFormaPago").val();
    send.accion = "I";
    send.accion_int = 2;
    send.fmp_id = fmp_id;
    send.totalFormaPago = totalFormaPago;
    send.banderafp = 1;
    send.estadoRetiro = banderaDesmontado;
    send.estadoSwitch = estadoSwitch;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            send = { auditoriaTarjeta: 1 };
            send.accion = "I";
            send.tipoTarjeta = fmp_descripcion;
            send.totalTarjeta = $("#txt_montoFormaPago").val();
            send.banderafp = 1;
            $.ajax({
                async: false,
                type: "GET",
                dataType: "json",
                contentType: "application/x-www-form-urlencoded",
                url: "config_desmontadoCajero.php",
                data: send,
                success: function(datos) {}
            });

            totalPosFormaPagoAgregada = 0;
            $("#btnT" + fmp_descripcion.replace(/\s/g, "") + "").val(parseFloat(totalFormaPago).toFixed(2));
            $("#inputT" + fmp_descripcion.replace(/\s/g, "") + "").val(parseFloat(totalPosFormaPagoAgregada).toFixed(2));
            diferenciaFormaPagoAgregada =
                parseFloat(totalFormaPago) - parseFloat(totalPosFormaPagoAgregada);
            $("#diferenciat" + fmp_descripcion.replace(/\s/g, "") + "").val(
                diferenciaFormaPagoAgregada.toFixed(2)
            );
            $("#ModalTecladoNuevaFormaPago").modal("hide");
            id_usuario = 0;
            fn_calculatotalesModificados(id_usuario, fmp_id);
            fn_totalesIngresados(id_usuario);
        }
    });
}

function fn_agregarNumerof(valor) {
    var cantidad = $("#txt_montoFormaPago").val();
    if (cantidad.length < 8) {
        //presionamos la primera vez punto
        if ((cantidad == "" || cantidad == 0) && valor == ".") {
            //si escribimos una coma al principio del número
            $("#txt_montoFormaPago").val("0."); //escribimos 0.
            coma = 1;
        } else {
            //continuar escribiendo un número
            if (valor == ".") {
                //si escribimos una coma decimal por primera vez; si intentamos escribir una segunda coma decimal no realiza ninguna acción
                if (coma == 0) {
                    cantidad = cantidad + "" + valor;
                    $("#txt_montoFormaPago").val(cantidad);
                    coma = 1; //cambiar el estado de la coma
                }
            }
            //Resto de casos: escribir un número del 0 al 9:
            else {
                var variable = cantidad;
                var indice = 0;
                indice = variable.indexOf(".") + 1;
                if (indice > 0) {
                    variable = variable.substring(indice, variable.length);
                    if (variable.length <= 2) {
                        cantidad = cantidad + "" + valor;
                        $("#txt_montoFormaPago").val(cantidad);
                        fn_focusFormasPago();
                    }
                } else {
                    cantidad = cantidad + "" + valor;
                    $("#txt_montoFormaPago").val(cantidad);
                    fn_focusFormasPago();
                }
            }
        }
    }
    fn_focusFormasPago();
}

function fn_focusFormasPago() {
    $("#txt_montoFormaPago").focus();
}

function fn_eliminarCantidadf() {
    var lc_cantidad = document.getElementById("txt_montoFormaPago").value.substring(0, document.getElementById("txt_montoFormaPago").value.length - 1);

    if (lc_cantidad == "" || lc_cantidad == ".") {
        coma = 0;
    }
    document.getElementById("txt_montoFormaPago").value = lc_cantidad;
    fn_focusFormasPago();
}

function fn_limpiarCantidadf() {
    $("#txt_montoFormaPago").val("");
    coma = 0;
}



function fn_eliminaFormasPagoAgregadas() {

    var send;
    send = { eliminaFormasPagoAgregadas: 1 };
    send.accion = "D";
    send.banderafp = 1;
    send.estadoRetiro = banderaDesmontado;
    send.estadoSwitch = 0;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {}
    });
}

function fn_imprime_CorteX() {
    if (debeRealizarTransferencia && valorTotalTransferenciaVenta > 0) {
        if (tipoLocalTransferenciaVenta === "ORIGEN") {
            alertify.alert("Antes de generar el CORTE EN X, debe realizar la transferencia de venta : " + valorTotalTransferenciaVenta);
            $("#alertify-ok").click(function() {});
        } else if (tipoLocalTransferenciaVenta === "DESTINO") {
            alertify.alert("Antes de generar el CORTE EN X, debe recibir la transferencia de venta: " + valorTotalTransferenciaVentaHeladeria);
            $("#alertify-ok").click(function() {
                location.reload();
            });
        }

        return false;
    }

    var send;
    usr_id = $("#hid_usuario").val();
    ctrc_id = $("#IDControlEstacion").val();
    send = { traeUsuarioAdmin: 1 };
    send.accion = 5;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                usr_id_admin = datos.usr_id;
                send = { InsertcanalmovimientoCorteX: 1 };
                send.usr_id = usr_id;
                send.ctrc_id = ctrc_id;
                send.usr_id_admin = usr_id_admin;
                var apiImpresion = getConfiguracionesApiImpresion();
                // console.log(apiImpresion);        
                if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
                    fn_cargando(1);                    
                    var result = new apiServicioImpresion('corteX',null,null, send);
                    var imprime = result["imprime"];
                    var mensaje = result["mensaje"];

                    console.log('imprime: ', imprime);
                    console.log('imprime: ', mensaje);

                    if (!imprime) {
                        alertify.success('Imprimiendo Corte X...');
                        
                    } else {
                        alertify.alert(mensaje);

                        alertify.success('Error al imprimir Corte X...');
                        fn_cargando(0);
                    }

                }else{

                    $.ajax({
                        async: false,
                        type: "GET",
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded",
                        url: "config_desmontadoCajero.php",
                        data: send,
                        success: function(datos) {
                            alertify.success("Imprimiendo Reporte....");
                        }
                    });
                }
            }
        }
    });
}

/***************************************************************************************************/
/*Funcion que calcula todos los valores de arqueo de dinero para guardarlos en arreglos            */
/***************************************************************************************************/
function fn_calculaValoresArqueo(arrayArqueo) {

    for (var i = 0; i < arrayArqueo.length; i++) {
        array_IDFormasPago[i] = $("#id_" + arrayArqueo[i] + "").val();
        array_transacciones[i] = $("#transacciones_" + arrayArqueo[i] + "").val();
        array_transaccionesIngresadas[i] = $("#transaccionesIngresadas" + arrayArqueo[i] + "").val();
    }

    for (var i = 0; i < arrayArqueo.length; i++) { //inicio del FOR		
        $("#montoActual" + arrayArqueo[i] + "").focus(function() { //inicio evento focus
            for (var i = 0; i < arrayArqueo.length; i++) {
                if ($("#montoActual" + arrayArqueo[i] + "").val() == "") {
                    $("#montoActual" + arrayArqueo[i] + "").val(0);
                }
                array_montoActual[i] = $("#montoActual" + arrayArqueo[i] + "").val();
                array_posCalculado[i] = $("#posCalculado" + arrayArqueo[i] + "").val();
                array_retirado[i] = $("#retirado_" + arrayArqueo[i] + "").val();
                array_diferencia[i] =
                    parseFloat(array_retirado[i]) +
                    parseFloat(array_montoActual[i]) -
                    parseFloat(array_posCalculado[i]);
                $("#diferencia" + arrayArqueo[i] + "").val(array_diferencia[i].toFixed(2));
            }
        });
    }
}

function fn_TarjetasDatafast(existe_efectivo = 1) {
    var send;
    var html = "";
    send = { "consultaformaPago": 1 };
    send.accion = 3;
    send.banderaDesmontado = banderaDesmontado;
    idformaPago = 0;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {

                if (existe_efectivo != 1) {
                    if (banderaDesmontado == 2 || banderaDesmontado == 1) {
                        html = "<thead><tr class='active'><th width='130px' style='text-align:center'>Formas de Pago</th><th width='130px' style='text-align:center'>Egresos</th><th width='130px' style='text-align:center'>Ingresos</th><th width='130px' style='text-align:center'>Retirado</th><th width='130px' style='text-align:center'>Transacciones</th><th style='text-align:center' width='130px'>Valor Declarado</th><th style='text-align:center' width='130px'>POS Calculado</th><th style='text-align:center' width='130px'>Diferencia</th></thead>";
                    } else {
                        html = "<thead>\n\
                                    <tr class='active'>\n\
                                        <th width='' style='text-align:center'>Formas de Pago</th>\n\
                                        <th width='' style='text-align:center'>Egresos</th>\n\
                                        <th width='' style='text-align:center'>Ingresos</th>\n\
                                        <th width='' style='text-align:center'>Retirado</th>\n\
                                        <th width='' style='text-align:center'>Transac.</th>\n\
                                        \n\<th width='' style='text-align:center'>Ingreso Transac.</th>\n\
                                        <th style='text-align:center' width=''>Valor Declarado</th>\n\
                                        <th style='text-align:center' width=''>POS Calculado</th>\n\
                                        <th style='text-align:center' width=''>Diferencia</th>\n\
                                    </tr>\n\
                                </thead>";
                    }
                }

                for (var i = 0; i < datos.str; i++) {
                    if (existe_efectivo != 1) {
                        idformaPago = datos[i]['fmp_id'];
                        idcontrolEstacion = datos[i]['ctrc_id'];

                        html += "<tr><td width='' align='center' style='vertical-align:middle'>TARJETAS</td>";
                    } else {
                        html = "<tr><td width='' align='center' style='vertical-align:middle'>TARJETAS</td>";
                    }

                    if (banderaDesmontado == 1) { //retiro de dinero
                        totalEfectivo = datos[i]['fpf_total_pagar'];
                        $("#hide_totalPosEfectivo").val(totalEfectivo);
                        //***********EGRESOS E INGRESOS*******************//
                        html += "<td><input inputmode='none'  id='' readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value='0.00'></td>";
                        html += "<td><input inputmode='none' id='' readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value='0.00'></td>";
                        //************************************************//
                        html += "<td><input inputmode='none' id='retirado" + datos[i]['fmp_descripcion'] + "' readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value='" + datos[i]['totalRetirado'].toFixed(2) + "'></td>";
                        html += "<td style='text-align:center;'><input inputmode='none' readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value=" + datos[i]['Transacciones'] + "></td>"; //
                        html += "<td><input inputmode='none' id='btnT" + datos[i]['fmp_descripcion'] + "' style='width:112px; height:60px' type='button' value='Presione Aqui' class='btn btn-primary'	onclick='fn_modalCuadreTarjetas(\"" + datos[i]['fmp_id'] + "\",\"" + datos[i]['ctrc_id'] + "\",\"" + datos[i]['usr_id'] + "\"," + datos[i]['totalRetirado'] + "," + datos[i]['Transacciones'] + "," + datos[i]['fpf_total_pagar'] + "," + datos[i]['diferenciaValor'] + "," + datos[i]['estadoSwitch'] + ")'></td>";
                        html += "<td><input inputmode='none' readonly='readonly' class='form-control' style='width:105px; text-align:center; height:60px' type='text' type='text' id='" + datos[i]['fmp_descripcion'] + "' value='" + datos[i]['fpf_total_pagar'].toFixed(2) + "'></td>";
                        html += "<td><input inputmode='none' class='form-control' readonly='readonly' style='width:105px;text-align:center; height:60px' type='text' id='diferenciat" + datos[i]['fmp_descripcion'] + "' value = '" + parseFloat(datos[i]['diferenciaValor']).toFixed(2) + "'></td>";
                        html += "<input inputmode='none' type='hidden' value=" + datos[i]['fmp_id'] + "></tr>";
                        $("#descipcionTarjetas").val(datos[i]['fmp_descripcion']);

                        if (existe_efectivo === 1) {
                            $("#formaPagoEfectivo").append(html);
                        } else {
                            $("#formaPagoEfectivo").html(html);
                        }
                    } else if (banderaDesmontado == 3) { //arqueo de dinero
                        totalEfectivo = datos[i]['fpf_total_pagar'];
                        $("#hide_totalPosEfectivo").val(totalEfectivo);
                        //***********EGRESOS E INGRESOS*******************//
                        html += "<td><input inputmode='none' id='' readonly='readonly' style='width:95px; height:60px; text-align:center;' class='form-control' value='0.00'></td>";
                        html += "<td><input inputmode='none' id='' readonly='readonly' style='width:95px; height:60px; text-align:center;' class='form-control' value='0.00'></td>";
                        //************************************************//
                        html += "<td><input inputmode='none' id='retirado" + datos[i]['fmp_descripcion'] + "' readonly='readonly' style='width:95px; height:60px; text-align:center;' class='form-control' value='" + datos[i]['totalRetirado'].toFixed(2) + "'></td>";
                        html += "<td style='text-align:center;'><input readonly='readonly' style='width:70px; height:60px; text-align:center;' class='form-control' value=" + datos[i]['Transacciones'] + "></td>"; //
                        html += "<td><input readonly='readonly' id='transaccionesIngresadas" + datos[i]['fmp_descripcion'].replace(/\s/g, "") + "' onkeypress='fn_numeros(event);' name='transacciones_" + datos[i]['fmp_descripcion'].replace(/\s/g, "") + "' onclick='' style='width:70; height:60px; text-align:center;' class='form-control' value='0'</td>"; //                                           
                        html += "<td><input inputmode='none' id='btnT" + datos[i]['fmp_descripcion'] + "' style='width:112px; height:60px' type='button' value='Presione Aqui' class='btn btn-primary'	onclick='fn_modalCuadreTarjetas(\"" + datos[i]['fmp_id'] + "\",\"" + datos[i]['ctrc_id'] + "\",\"" + datos[i]['usr_id'] + "\"," + datos[i]['totalRetirado'] + "," + datos[i]['Transacciones'] + "," + datos[i]['fpf_total_pagar'] + "," + datos[i]['diferenciaValor'] + "," + datos[i]['estadoSwitch'] + ")'></td>";
                        html += "<td><input readonly='readonly' class='form-control' style='width:95px; text-align:center; height:60px' type='text' type='text' id='" + datos[i]['fmp_descripcion'] + "' value='" + datos[i]['fpf_total_pagar'].toFixed(2) + "'></td>";
                        html += "<td><input inputmode='none'  class='form-control' readonly='readonly' style='width:95px;text-align:center; height:60px' type='text' id='diferenciat" + datos[i]['fmp_descripcion'] + "' value = '" + parseFloat(datos[i]['diferenciaValor']).toFixed(2) + "'></td>";
                        html += "<input type='hidden' value=" + datos[i]['fmp_id'] + "></tr>";
                        $("#descipcionTarjetas").val(datos[i]['fmp_descripcion']);

                        if (existe_efectivo === 1) {
                            $("#formaPagoEfectivo").append(html);
                        } else {
                            $("#formaPagoEfectivo").html(html);
                        }
                    } else if (banderaDesmontado == 2) { //desmontado
                        //***********EGRESOS E INGRESOS*******************//
                        html += "<td><input id='' readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value='0.00'></td>";
                        html += "<td><input id='' readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value='0.00'></td>";
                        //************************************************//
                        html += "<td><input inputmode='none' id='retirado" + datos[i]['fmp_descripcion'] + "' readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value='" + datos[i]['totalRetirado'].toFixed(2) + "'></td>";
                        html += "<td style='text-align:center;'><input readonly='readonly' style='width:105px; height:60px; text-align:center;' class='form-control' value=" + datos[i]['Transacciones'] + "></td>"; //
                        html += "<td><input inputmode='none' id='btnT" + datos[i]['fmp_descripcion'] + "' style='width:112px; height:60px' type='button' value='Presione Aqui' class='btn btn-primary'	onclick='fn_modalCuadreTarjetas(\"" + datos[i]['fmp_id'] + "\",\"" + datos[i]['ctrc_id'] + "\",\"" + datos[i]['usr_id'] + "\"," + datos[i]['totalRetirado'] + "," + datos[i]['Transacciones'] + "," + datos[i]['fpf_total_pagar'] + "," + datos[i]['diferenciaValor'] + "," + datos[i]['estadoSwitch'] + ")'></td>";
                        html += "<td><input inputmode='none' readonly='readonly' class='form-control' style='width:; text-align:center; height:60px' type='text' type='text' id='" + datos[i]['fmp_descripcion'] + "' value='" + datos[i]['fpf_total_pagar'].toFixed(2) + "'></td>";
                        html += "<td><input inputmode='none' class='form-control' readonly='readonly' style='width:;text-align:center; height:60px' type='text' id='diferenciat" + datos[i]['fmp_descripcion'] + "' value = '" + parseFloat(datos[i]['diferenciaValor']).toFixed(2) + "'></td>";
                        html += "<input type='hidden' value=" + datos[i]['fmp_id'] + "></tr>";
                        $("#descipcionTarjetas").val(datos[i]['fmp_descripcion']);

                        if (existe_efectivo === 1) {
                            $("#formaPago").append(html);
                        } else {
                            $("#formaPago").html(html);
                        }
                    }

                    if (existe_efectivo === 1) {
                        numeroFormasPago++;
                    }

                    id_botones.push("btnT" + datos[i]['fmp_descripcion'].replace(/\s/g, ""));
                }

                if (existe_efectivo != 1) {
                    if (banderaDesmontado == 2) {
                        $("#modal_formaPago").modal('show');
                        $("#hid_controlEfectivo").val(0);

                        //FIN FUNCION BOTON CANCELAR AL DESMONTAR CAJERO
                        fn_cargaTotal(idcontrolEstacion, idformaPago);
                        $("#valorEfectivoTotal").val();
                        $("#totalPosCalculado").val();
                        diferenciaTotalFormasPago = parseFloat($("#valorEfectivoTotal").val()) - parseFloat($("#totalPosCalculado").val());
                    } else {
                        $("#modal_formaPagoEfectivo").modal("show");
                        $("#btn_cancelarEfectivo").click(function() {
                            $("#modal_formaPagoEfectivo").modal("hide");
                            $("#hid_controlRetiroEfectivo").val(0);
                            $("#tpieEfectivo tr:eq(0) td:eq(3)").html('');
                        });
                    }
                }
            } else {
                if (existe_efectivo != 1) {
                    if (banderaDesmontado === 2) {
                        alertify.set({ labels: { ok: "SI", cancel: "NO" } });
                        alertify.confirm("No existen transacciones del empleado asignado, desea retirarlo?");

                        $("#alertify-ok").click(function() {
                            send = { "actualizaCajero": 1 };
                            send.accion = 'U';
                            send.usuario = $("#hid_usuario").val();
                            $.ajax({
                                async: false,
                                type: "GET",
                                dataType: "json",
                                contentType: "application/x-www-form-urlencoded",
                                url: "config_desmontadoCajero.php",
                                data: send,
                                success: function(datos) {
                                    usuarioCajero = $("#hid_usuarioDescripcion").val();
                                    send = { "auditoriaCajero": 1 };
                                    send.accion = 'I';
                                    send.accion_int = 2;
                                    send.usuarioCajero = $("#hid_usuarioDescripcion").val();
                                    $.ajax({
                                        async: false,
                                        type: "GET",
                                        dataType: "json",
                                        contentType: "application/x-www-form-urlencoded",
                                        url: "config_desmontadoCajero.php",
                                        data: send,
                                        success: function(datos) {
                                            alertify.alert("Desmontado exitoso");
                                            window.location.replace("../index.php");
                                        }
                                    });
                                }
                            });
                        });
                    } else {
                        alertify.alert("No existen transacciones del empleado asignado.", function() {
                            return false;
                        });
                    }
                }
            }
        }
    });
}

function fn_modalCuadreTarjetas() {
    var send;
    if (banderaDesmontado == 1) {
        $("#titulomodalCuadreTarjetas").html("RETIROS - TARJETAS");
    } else if (banderaDesmontado == 2) {
        $("#titulomodalCuadreTarjetas").html("DESASIGNAR CAJERO - TARJETAS");
    } else if (banderaDesmontado == 3) {
        $("#titulomodalCuadreTarjetas").html("ARQUEO - TARJETAS");
    }

    send = { consultaformaPago: 1 };
    send.accion = 4;
    send.banderaDesmontado = banderaDesmontado;
    idformaPago = 0;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            if (banderaDesmontado == 3) {
                var html = "<thead><tr class='active'><th width='130px' style='text-align:center'>Formas de Pago</th><th width='130px' style='text-align:center'>Retirado</th><th width='130px' style='text-align:center'>Transacciones</th></th><th width='130px' style='text-align:center'>Ingreso Transacciones</th><th style='text-align:center' width='130px'>Valor Declarado</th><th style='text-align:center' width='130px'>POS Calculado</th><th style='text-align:center' width='130px'>Diferencia</th></thead>";
            } else {
                var html = "<thead><tr class='active'><th width='130px' style='text-align:center'>Formas de Pago</th><th width='130px' style='text-align:center'>Retirado</th><th width='130px' style='text-align:center'>Transacciones</th><th style='text-align:center' width='130px'>Valor Declarado</th><th style='text-align:center' width='130px'>POS Calculado</th><th style='text-align:center' width='130px'>Diferencia</th></thead>";
            }

            if (datos.str > 0) {
                numeroFormasPago = datos.str;
                for (var i = 0; i < datos.str; i++) {
                    arrayArqueoCuadreTarjetas[i] = datos[i]["fmp_descripcion"].replace(
                        /\s/g,
                        ""
                    );
                    html +=
                        "<tr><td width='180' align='center' style='vertical-align:middle'>" +
                        datos[i]["fmp_descripcion"] +
                        "</td>";
                    html +=
                        "<td><input inputmode='none' id='retiro" +
                        datos[i]["fmp_descripcion"] +
                        "' readonly='readonly' style='width:120px; height:60px; text-align:center;' class='form-control' value='" +
                        datos[i]["totalRetirado"].toFixed(2) +
                        "'></td>";
                    html +=
                        "<td style='text-align:center;'><input readonly='readonly' style='width:120px; height:60px; text-align:center;' class='form-control' value=" +
                        datos[i]["Transacciones"] +
                        "></td>"; //
                    if (banderaDesmontado == 3) {
                        html +=
                            "<td><input inputmode='none' id='transaccionesIngresadas" +
                            datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                            "' onkeypress='fn_numeros(event);' name='transacciones_" +
                            datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                            "' onclick='fn_borrarcantidadTeclado(\"" +
                            datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                            "\");' style='width:130; height:65px; text-align:center;' class='form-control' value='0'</td>"; //
                    }
                    html +=
                        "<td><input inputmode='none' id='btnT" +
                        datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                        "_datafast' style='width:120px; height:60px' type='button' value='Presione Aqui' class='btn btn-primary'	onclick='fn_modalTarjetas(\"" +
                        datos[i]["fmp_id"] +
                        '","' +
                        datos[i]["ctrc_id"] +
                        '","' +
                        datos[i]["usr_id"] +
                        '",' +
                        datos[i]["totalRetirado"] +
                        "," +
                        datos[i]["Transacciones"] +
                        "," +
                        datos[i]["fpf_total_pagar"] +
                        "," +
                        datos[i]["diferenciaValor"] +
                        "," +
                        datos[i]["estadoSwitch"] +
                        ")'></td>";
                    html +=
                        "<td><input inputmode='none' readonly='readonly' class='form-control' style='width:120px; text-align:center; height:60px' type='text' type='text' id='" +
                        datos[i]["fmp_descripcion"] +
                        "' value='" +
                        datos[i]["fpf_total_pagar"].toFixed(2) +
                        "'></td>";
                    html +=
                        "<td><input inputmode='none' class='form-control' readonly='readonly' style='width:120px;text-align:center; height:60px' type='text' id='diferenciat" +
                        datos[i]["fmp_descripcion"] +
                        "_datafast' value = '" +
                        parseFloat(datos[i]["diferenciaValor"]).toFixed(2) +
                        "'></td>";
                    html += "<input inputmode='none' type='hidden' value=" + datos[i]["fmp_id"] + "></tr>";
                    idcontrolEstacion = datos[i]["ctrc_id"];
                    $("#hid_controlEstacion").val(datos[i]["ctrc_id"]);
                    $("#cuadreTarjetas").html(html);
                    id_botonesCuadreTarjetas[i] =
                        "btnT" +
                        datos[i]["fmp_descripcion"].replace(/\s/g, "") +
                        "_datafast";
                }
                fn_tecladoTransacciones(arrayArqueoCuadreTarjetas);
                $("#modal_cuadreTarjetas").modal("show");
                $("#valorEfectivoTotal").val();
                $("#totalPosCalculado").val();
                diferenciaTotalFormasPago = parseFloat($("#valorEfectivoTotal").val()) - parseFloat($("#totalPosCalculado").val());
            } else {
                alertify.alert("No existen datos de Tarjetas");
                $("#alertify-ok").click(function() {
                    return false;
                });
            }
        }
    });
}

function fn_cerrarModalCuadreTarjetas() {
    if (banderaDesmontado === 2 || banderaDesmontado === 1) {
        for (var i = 0; i < id_botonesCuadreTarjetas.length; i++) {
            if (isNaN($("#" + id_botonesCuadreTarjetas[i] + "").val())) {
                alertify.alert("Faltan val&oacute;res por ingresar");
                return false;
            }
        }
    }
    var send;
    send = { consultaformaPago: 1 };
    send.accion = 5;
    send.banderaDesmontado = banderaDesmontado;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {

                    if (banderaDesmontado === 1 || banderaDesmontado === 3) {
                        $("#modal_formaPagoEfectivo #btnT" + datos[i]['fmp_descripcion'] + "").val(parseFloat(datos[i]['totalRetirado']).toFixed(2));
                        $("#modal_formaPagoEfectivo #diferenciat" + datos[i]['fmp_descripcion'] + "").val(datos[i]['diferenciaValor'].toFixed(2));
                        $("#modal_formaPagoEfectivo #transaccionesIngresadas" + datos[i]['fmp_descripcion'] + "").val(datos[i]['transaccionesIngresadas']);
                    } else {
                        $("#modal_formaPago #btnT" + datos[i]['fmp_descripcion'] + "").val(parseFloat(datos[i]['totalRetirado']).toFixed(2));
                        $("#modal_formaPago #diferenciat" + datos[i]['fmp_descripcion'] + "").val(datos[i]['diferenciaValor'].toFixed(2));
                        $("#modal_formaPago #transaccionesIngresadas" + datos[i]['fmp_descripcion'] + "").val(datos[i]['transaccionesIngresadas']);
                    }
                }
            }
        }
    });
    $("#modal_cuadreTarjetas").modal("hide");
}

function fn_interfaceConsultaTransferencia() {
    var send;
    rst_id = $("#hid_restaurante").val();
    cedulaCajero = $("#cedulaCajero").val();
    fecha = $("#fecha").val();

    console.log("ingreso a interface transferencia personal..");
    send = { validaTransferencia: 1 };
    send.cedulaCajero = cedulaCajero;
    send.rst_id = rst_id;
    send.fecha = fecha;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../serviciosweb/interface/config_cliente_servicio.php",
        data: send,
        success: function(datos) {
            if (!datos) {
                alertify.error("Error al recuperar la información");
            } else {
                if (datos["Respuesta"] == 1) {
                    console.log(datos["mensaje"]);
                    //Finalizar proceso 
                    TRANSFERENCIAPERSONAL = datos["Respuesta"];
                    MENSAJE = datos["mensaje"];
                    fn_desmontarCajeroArqueo();
                } else if (datos["Respuesta"] == 0) {
                    //                    console.log(datos['mensaje']);
                    //                    TRANSFERENCIAPERSONAL = datos['Respuesta'];
                    //                    MENSAJE = datos['mensaje'];
                    //                    alertify.confirm(MENSAJE, function (e) {
                    //                        if (e) { //puede seguir facturando
                    fn_desmontarCajeroArqueo();
                    //}
                    //});
                } else {
                    fn_desmontarCajeroArqueo();
                    //alertify.alert('Existe problemas al conectarse al servicio, se consultara posteriomente.');
                    //console.log('ingreso a interface transferencia..');
                }
            }
        }
    });
}

function fn_obtieneUsuarioAdministrador() {
    var send;
    var IDUsuarioCajero = $("#hid_usuario").val();
    var IDControlEstacion = $("#IDControlEstacion").val();

    send = { traeUsuarioAdmin: 1 };
    send.accion = 5;
    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php",
        data: send,
        success: function(datos) {
            if (datos.str > 0) {
                var IDUsuarioAdmin = datos.usr_id;
                fn_impresionArqueroCaja(IDUsuarioCajero, IDControlEstacion, IDUsuarioAdmin);
            }
        }
    });
}

function fn_impresionArqueroCaja(usuarioCajero, IDControlEstacion, usuarioAdmin) {
    var send;
    send = { canalMovimientoArqueo: 1 };
    send.usr_id = usuarioCajero;
    send.ctrc_id = IDControlEstacion;
    send.usr_id_admin = usuarioAdmin;
    var apiImpresion = getConfiguracionesApiImpresion();
    if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {
        fn_cargando(1);
        var result = new apiServicioImpresion('arqueo',null,null, send);
        var imprime = result["imprime"];
        var mensaje = result["mensaje"];

        console.log('imprime: ', imprime);
        console.log('imprime: ', mensaje);

        if (!imprime) {
            alertify.success('Imprimiendo Arqueo...');
            fn_cargando(0);
            $("#hid_controlRetiroEfectivo").val(0);
            $("#modal_formaPagoEfectivo").modal("hide");
            $("#hide_totalBilletesEfectivo").val("");  

        } else {
            alertify.alert(mensaje);
            alertify.success('Error al imprimir Arqueo...');
            fn_cargando(0);
            $("#hid_controlRetiroEfectivo").val(0);
            $("#modal_formaPagoEfectivo").modal("hide");
            $("#hide_totalBilletesEfectivo").val("");
        }

    }else{
        $.ajax({
            async: false,
            type: "GET",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "config_desmontadoCajero.php",
            data: send,
            success: function(datos) {
                alertify.success("Imprimiendo Arqueo....");
            }
        });
    }
}

function fn_reversarCCL() {
    alertify.confirm("Reversando Monto Caja Chica Local <br> Esta seguro de realizar esta acción?", function(e) {
        if (!e) {

        } else {
            var send;
            send = { reversarCCL: 1 };
            $.ajax({
                async: false,
                type: "GET",
                dataType: "json",
                contentType: "application/x-www-form-urlencoded",
                url: "config_desmontadoCajero.php",
                data: send,
                success: function(datos) {
                    fn_reversoCCLWEB(
                        datos[0]["fechaInicia"],
                        datos[0]["fechaFinaliza"]
                    );
                }
            });
        }
    });
}

function fn_reversoCCLWEB(fechaInicia, fechaFinaliza) {
    cargando(1);
    var send;
    send = { verificarCCLCancelacion: 1 };
    send.fechaInicia = fechaInicia;
    send.fechaFinaliza = fechaFinaliza;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "xml",
        contentType: "application/x-www-form-urlencoded",
        url: "../movimientos/config_movimientos.php",
        data: send,
        success: function(datos) {
            cargando(0);
            alertify.success("Reversando Monto Caja Chica Local Gerente....\n");
            //fn_consultaFormaPago();
            //$("#modal_formaPagoEfectivo").modal("hide");
            fn_formaPagoEfectivo(1);
            estadoCCL = 0;
            $("#botonCCL").html('<button type="button" id="btn_cajachicalocal" class="btn btn-warning" style="height:70px;width:180px;" onclick="fn_modal_cajaChicaLocal();" >Caja Chica <br /> Tienda</button>');
            if (datos.mensaje === "Servicio no disponible.") {
                alertify.error("Imposible Reversar Monto Caja Chica Local Gerente....");
                //fn_consultaFormaPago();
                fn_formaPagoEfectivo(1);
                //$("#modal_formaPagoEfectivo").modal("hide");
                $("#botonCCL").html('<button type="button" id="btn_cajachicalocal" class="btn btn-warning" style="height:70px;width:180px;" onclick="fn_modal_cajaChicaLocal();" >Caja Chica <br /> Tienda</button>');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert(" jq " + jqXHR + " status " + textStatus + " error " + errorThrown);
        }
    });
}
$("#btn_cancelarEfectivo").click(function() {

    $("#botonCCL").html('<button type="button" id="btn_cajachicalocal" class="btn btn-warning" style="height:70px;width:180px;" onclick="fn_modal_cajaChicaLocal();" >Caja Chica <br /> Tienda</button>');
    $("#btn_cajachicalocal1").remove();
    $("#btn_cajachicalocal2").remove();
});
function fn_retiroCashless() { 
    send = { retiroCashless: 1 };
      $.ajax({
          async: false,
          type: "GET",
          dataType: "json",
          contentType: "application/x-www-form-urlencoded",
          url: "config_desmontadoCajero.php",
          data: send,
          success: function (datos) {
              if (datos.str > 0) {
                  alert(datos.str);
              }
          }
      });
  }
  function fn_eliminaFormasPagoAgregadasCashless() {

    var send;
  send = { eliminaFormasPagoAgregadasCashless: 1 };
  send.accion = "C";
    send.banderafp = 1;
    send.estadoRetiro = banderaDesmontado;
    send.estadoSwitch = 0;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded",
        url: "config_desmontadoCajero.php", data: send, success: function (datos) {
        }
    });
}


function fn_apiAperturaCajon(codigo_formaPago){

    // Aplicar apertura cajon
    var apiImpresion = getConfiguracionesApiImpresion();
    if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1) {

        send = { "servicioApiAperturaCajon": 1 };
        send.idFormaPago = codigo_formaPago;

        $.getJSON("../facturacion/config_servicioApiAperturaCajon.php", send, function (datos) { 

            console.log(datos);

            });

        }

}
