/* global alertify */

///////////////////////////////////////////////////////////
///////DESARROLLADO POR: Cristhian Castro ////////////////////
///////DESCRIPCION: ///////////////////
///////TABLAS INVOLUCRADAS: Menu_Agrupacion, ////////
////////////////Menu_Agrupacionproducto////////
////////////////Detalle_Orden_Pedido////////
///////////////////Plus, Precio_Plu, Mesas///////////////////
///////FECHA CREACION: 28-02-2014//////////////////////////
///////FECHA ULTIMA MODIFICACION: 07-05-2014////////////////
///////USUARIO QUE MODIFICO:  Cristhian Castro///////////////
///////DECRIPCION ULTIMO CAMBIO: Documentación y validación en Internet Explorer 9+///////
/////////////////////////////////////////////////////////// 
/////////////////////////////////////////////////////////// 
/*----------------------------------------------------------------------------------------------------
 Función que se ejecuta al cargar la página de separación de cuentas
 -----------------------------------------------------------------------------------------------------*/
/////////////////////////////////////////////////////////// M
//var acum_split = 1;
var cant_split = 1;
var acum_split_max = 1;
var acum_split_min = 1;
var dividir_producto = false; // el proceso esta activo ?
var hay_producto_seleccionado = false; // selecciono un producto ?
var val = [];
var primer_split = 0;
var primer_splitBoolean = true;
var cantidadSeleccionados = 0;
var cantidadPlus = 1; // Para conocer la cantidad de items que hay actualmente del plu en el detalle.
var cantidadDeDiviciones = 0;
var anterior_ancestro = '';
var anterior_split = 0;
var ancestro = "";
var cambiarplu = 0;
var array_cuentas = [];
function mostrarModal() {
    Accion = 0;
    $("#aumentarContador").dialog("open");
    $("#cantidad").val('');

}
$(document).ready(function () {
    $("#aumentarContador").hide();
    $('#imgNext').hide(100);
    $('#imgBack').hide(100);
    $("#formCobrar").hide();
    $(".hide_impuesto").hide();
    fn_LoadAccess($('#hide_usr_id').val(), 'SEPARAR CUENTAS');
    fn_cargarTotal_cuentaSeparadas();
    var cdn_tipoimpuesto = $("#hide_cdn_tipoimpuesto").val();
    if (cdn_tipoimpuesto == 1) {
        $(".hide_impuesto").show();
    }
    $("#barraPrincipal").append('<button id="split_acumulado" class="right" >Agregar Mesa</button>');
    $("#split_acumulado").click(function () {
        acum_split_max += 1;
        cantidadDeDiviciones++;
        fn_split_cuenta(acum_split_max);
        $('#masterListadoPedido' + acum_split_max).shortscroll();
    });
});

var listaPermisos = new Array();
var boolean = true;
function fn_LoadAccess(usuario_id, pantalla) {

    send = {"cargarPermisos": 1};
    send.usuario_id = usuario_id;
    send.pantalla = pantalla;
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../seguridades/config_usuario.php", data: send,
        success: function (datos)
        {
            if (datos.str > 0)
            {
                for (var i = 0; i < datos.str; i++) {
                    if (document.getElementsByName(datos[i]['descripcion'])[0] !== undefined) {
                        var IDBoton = document.getElementsByName(datos[i]['descripcion'])[0].id;
                        $('#' + IDBoton).removeAttr('disabled');
                        $('#' + IDBoton).removeClass('classDisabled');
                    }
                    listaPermisos[i] = datos[i]['descripcion'];
                }
            } else
            {
                alertify.error('No cuenta con ningún permiso sobre ésta pantalla.');
            }
        }});
}

function fn_cargarTotal_cuentaSeparadas(){
    var send;
    var guardarCop = {"guardarCop": 1};
    var odp_id = $("#hide_odp_id").val();
    send = guardarCop;

    send.mesa_id = $("#hide_mesa_id").val();
    send.odp_id = odp_id;
    $.getJSON("config_separarCuentas.php", send, function (datos) {
        if (datos.str > 0) {
            array_cuentas = datos;
            fn_MaxyMin(odp_id);
            //setInterval(fn_inicio(), 1000);
            fn_inicio();
        }
    });
}

function fn_MaxyMin(odp_id) {
    var send_num;
    var numeroCuentas = {"numeroCuentas": 1, "odp_id": odp_id};
    send_num = numeroCuentas;
    $.ajax({async: false, url: "config_separarCuentas.php", data: send_num, dataType: "json", success: function (num) {
            if (num.str > 0) {
                acum_split_max = num[0]["acum_split_max"];
                acum_split_min = num[0]["acum_split_min"];
            }
        }
    });
}
function fn_inicio() {
    var idc = acum_split_min;
    $(".listado ul#id" + idc + "").empty();
    //for (idc; idc <= acum_split_max; idc++) {
    for (i in array_cuentas) {
        idc = array_cuentas[i].dop_cuenta;
        fn_listaPendiente_impuesto(idc);
        cantidadDeDiviciones++;
        fn_split_cuenta(idc);
        $('#masterListadoPedido' + idc).shortscroll();
        var send;
        var listaPendiente = {"listaPendiente": 1};
        send = listaPendiente;
        send.cdn_id = $("#hide_cdn_id").val();
        send.odp_id = $("#hide_odp_id").val();
        send.cat_id = $("#hide_cat_id").val();
        send.rst_id = $("#hide_rst_id").val();
        send.dop_cuenta = idc;
        $.ajax({async: false, url: "config_separarCuentas.php", data: send, dataType: "json", success: function (datos) {
                if (datos.str > 0) {
                    $(".listado ul#id" + idc + "").empty();
                    var subTotalFinal = 0;
                    var basedoceFinal = 0;
                    var baseceroFinal = 0;
                    var IvaFinal = 0;
                    var TotalFinal = 0;
                    var html = "";
                    for (var i = 0; i < datos.str; i++) {
                        html = "<li id=" + datos[i]["dop_id"] + " id_detalle_padre= '"+datos[i]['IDDetalleOrdenPedidoPadre']+"'  ancestro='" + datos[i]["ancestro"] + "' onclick=\"fn_seleccionarItem('" + datos[i]["ancestro"] + "' , " + idc + "   )\" data-id=" + datos[i]["dop_id"] + "><div class='listaproductosCant'>" + datos[i]["dop_cantidad"] + "</div><div class='listaproductosDesc'>" + datos[i]["magp_desc_impresion"] + "</div><div class='listaproductosVal'>$" + (datos[i]["dop_precio_unitario"] * datos[i]["dop_cantidad"]).toFixed(2) + "</div></li>";
                        $(".listado ul#id" + idc + "").append(html);
                    }
                    noSelectBeneficio();
                }
            }
        });
    }
}
/*----------------------------------------------------------------------------------------------------
 Función que permite seleccionar un item y cambiarlo hacia otra cuenta y cambia el estado
 -----------------------------------------------------------------------------------------------------*/
function fn_listaPendiente_impuesto(idc) {
    var send1;
    send1 = {"listaPendiente_impuesto": 1};
    send1.odp_id = $("#hide_odp_id").val(); //send.odp_id;
    send1.idc = idc;
    $.ajax({async: true, url: "config_separarCuentas.php", data: send1, dataType: "json", success: function (data) {
            if (data.str > 0) {
                $("#subTotal_id" + idc).html(data[0]['SUBTOTAL'].toFixed(2));
                $("#Iva_id" + idc).html(data[0]['IVA'].toFixed(2));
                $("#Total_id" + idc).html(data[0]['TOTAL'].toFixed(2));
            }
        }
    });
}

function fn_validarSplitEnCero(dop_id, num_split){
    let continuar = false;
    var send = {"validarCuentaEnCero": 1};
    send.dop_id = dop_id;
    send.dop_cuenta = num_split;
    send.IDEstacion = $("#hide_est_id").val();
    $.ajax({
        async: false, 
        type: "POST", 
        dataType: 'json', 
        contentType: "application/x-www-form-urlencoded", 
        url: "config_separarCuentas.php",
        data: send,
        success: function (datos)
        {
            if (datos.str > 0) {
                if(datos[0]['total_detino'] == 0 && datos[0]['total'] > 0){
                    continuar = true;
                }else if(datos[0]['total_detino'] > 0){
                    continuar =  true;
                }
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log("Error");
        }
    });
    return continuar;
}

function fn_seleccionarItem(dop_id, num_split) {
    /*let continuar = fn_validarSplitEnCero(dop_id, num_split);
    if(!continuar){
        alertify.error('No se permite mover ya que la cuenta origen quedaria con valor $ 0.00.');
        return;
    }
    //return;*/
    //Obtengo los anteriores valores en caso de que el usuario presione cancelar.
    if (dop_id !== 'null') {

        anterior_ancestro = dop_id;
        anterior_split = num_split;

        if (dividir_producto) {
            if (primer_splitBoolean) {
                primer_split = num_split;
                primer_splitBoolean = false;
            }
            dop_id_seleeccionado = dop_id;
            alertify.success('Seleccione los splits');
            hay_producto_seleccionado = true;
            $('#split_dividir').html('Finalizar División');
        }

        $(".listadoPedido li").removeClass("focus");
        ancestro = $('#' + dop_id).attr("ancestro");
        $('#' + dop_id).addClass("focus");
        if (ancestro.length > 0 && ancestro !== undefined && ancestro !== null) {

            $('[ancestro="' + ancestro + '"]').addClass('focus');
            $('[id_detalle_padre="' + dop_id + '"]').addClass('focus');

            var idsDetallesSeleccionados = [];
            // Seleccionamos todos los <li> que tienen la clase 'focus'
            $('li.focus').each(function() {
                var id = $(this).data('id');
                idsDetallesSeleccionados.push(id);
            });


            //  if (existe('Mover plus entre splits', listaPermisos)) {
            $('[ancestro="' + ancestro + '"]').live("click", function () {
                $('#' + dop_id).addClass("focus");
                dop_id_seleeccionado = dop_id;
                val.push(dop_id_seleeccionado);
                validaDividirCuenta = 1;
            });

            $('[id_detalle_padre="' + dop_id + '"]').live("click", function () {
                $('#' + dop_id).addClass("focus");
                dop_id_seleeccionado = dop_id;
                val.push(dop_id_seleeccionado);
                validaDividirCuenta = 1;
            });
            fn_alerta(ancestro);
        }

    } else {
        if (dividir_producto) {
            alertify.alert('El producto seleccionado no se puede dividir debido a que proviene de una división de cuentas.');
        }
    }

}
function fn_alerta(ancestro_id) {
    if (!dividir_producto) {
        //alertify.confirm("Desea mover el plu?");
        //$("#alertify-ok").click(function () {
        cambiarplu = 1;
        cantidadPlus = $('#' + ancestro_id).children('div .listaproductosCant').html();
        if (cantidadPlus > 1) {
            mostrarModal();
        }
        // });
        $("#alertify-cancel").click(function () {
            fn_cancelarAgregarCantidad1();
        });
    }
}

function fn_cancelarAgregarCantidad1() {
    Accion = 0;
    lc_cantidad = 1;
    $("#cantidad").val('');
    $("#etiqueta_cantidad").val('x1');
    $("#aumentarContador").dialog("close");
    $("#keyboard").hide();
    $("#cantidadOK").val('0');
    fn_verificarPlu(anterior_ancestro, anterior_split);

}
function fn_okCantidad1() {
    coma = 0;
    var cantidad = $("#cantidad").val();

    if (parseFloat(cantidad) < 1000 && parseFloat(cantidad) > 0) {
        if (parseFloat(cantidad) <= cantidadPlus) {


            lc_cantidad = cantidad;
            $("#aumentarContador").dialog("close");
            $("#cantidad").val("");
            $("#cantidadOK").val(cantidad);

            fn_verificarPlu(prm_acestro_id, prm_idfinal);
            // Reset var.
        } else {
            alertify.alert('La cantidad ingresada supera el la actual en el pedido.');
        }

    } else {
        alertify.alert('Ingrese un cantidad v&aacute;lida.');
        $("#cantidad").val("");
    }
}

function fn_VisualizaBoton() {
    var send = {"VisualizaBoton": 1};
    send.rst_id = $("#hide_rst_id").val();
    send.IDEstacion = $("#hide_est_id").val();
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "config_ordenPedido.php", data: send,
        success: function (datos)
        {
            if (datos.str > 0)
            {
                if (datos[0]["tomaPedido"] === 'Si') {
                    $("#cobrar").hide();
                    $("#cobrarVoucher").hide();
                    $(".permisoCobrar").hide();
                } else {

                    if (datos[0]["Activo"] === 1)
                    {
                        $("#cobrarVoucher").show();
                    } else {
                        $("#cobrarVoucher").hide();
                    }
                }
            } else
            {
                $("#cobrarVoucher").hide();
                $("#cobrar").hide();
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }});

}
/*----------------------------------------------------------------------------------------------------
 Función que permite obtener el id de la subcuenta
 -----------------------------------------------------------------------------------------------------*/
var dato = '-1';
function setDividido(data) {
    dato = data;
}
function GetDividido() {
    return dato;
}

function  estaDividido(dop) {
    var send = {"estaDividido": 1};
    send.dop_id = dop;
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "config_separarCuentas.php", data: send,
    success: function (datos){
        if (datos.str > 0){
            setDividido(datos[0]['respuesta']);
            noSelectBeneficio();
        } else{
            setDividido('0');
        }
    }});
}

var prm_acestro_id = '';
var prm_idfinal = '';
function fn_obtenerID() {
    if (existe('Mover plus entre splits', listaPermisos)) {
        $("#contenedorTotal .listadoPedido").sortable({
            connectWith: "#contenedorTotal .listadoPedido",
            containment: "#contenedorTotal",
            receive: function (event, ui) {
                var idfinal = this.id.replace(/id/, "");
                var dop_id = ui.item.closest("[id]").attr("id");
                var acestro_id = ui.item.closest("[ancestro]").attr("ancestro");
                estaDividido(dop_id);
                /*let continuar = fn_validarSplitEnCero(dop_id, '');
                if(!continuar){
                    alertify.error('No se permite mover ya que la cuenta origen quedaria con valor $ 0.00.');
                    regresarProductoaCuenta(dop_id, true);
                    return;
                }*/
                if (GetDividido() === '0' && acestro_id !== 'null') {
                    cantidadPlus = $('#' + acestro_id).children('div .listaproductosCant').html();
                    prm_acestro_id = acestro_id;
                    prm_idfinal = idfinal;
                    if (cantidadPlus > 1) {
                        $("#aumentarContador").dialog("open");
                    } else {
                        fn_verificarPlu(acestro_id, idfinal);
                    }
                } else {
                    regresarProductoaCuenta(dop_id);
                }
            }
        }).disableSelection();
    }
}

function  regresarProductoaCuenta(dop_id, soloMover = false) {
    if(!soloMover){
        alertify.error('No se permite mover a otra cuenta productos divididos.');
    }
    var onclk = $('#' + dop_id).attr('onclick');
    var dopCuenta = (onclk.substring(onclk.lastIndexOf(',') + 1, onclk.lastIndexOf(')'))).trim();

    var html = $('#contenedorTotal #ct' + dopCuenta + ' .listadoPedido').html( );
    var li = "<li id=\"" + $('#' + dop_id).attr('id') + "\" ancestro=\"null\" onclick=\"" + $('#' + dop_id).attr('onclick') + "\" data-id=\"" + $('#' + dop_id).attr('data-id') + "\">";

    html = html + li + $('#' + dop_id).html() + "</li>";
    $('#' + dop_id).remove();
    $('#contenedorTotal #ct' + dopCuenta + ' .listadoPedido').html(html);

}
/*----------------------------------------------------------------------------------------------------
 Función que verifica si el plu seleccionado ya existe en la cuenta para realizar el cálculo
 -----------------------------------------------------------------------------------------------------*/
function fn_verificarPlu(dop_id, dop_cuenta) {
    if(dop_id){
        let continuar = fn_validarSplitEnCero(dop_id, dop_cuenta);
        if(!continuar){
            alertify.error('No se permite mover ya que la cuenta destino quedaria con valor $ 0.00.');
            return;
        }
    }
    

    $("#mdl_rdn_pdd_crgnd").show();
    var send;
    var mesa = $("#hide_mesa_id").val();
    var verificarDop = {"verificarDop": 1};

    send = verificarDop;
    send.odp_id = document.getElementById("hide_odp_id").value;
    send.dop_id = dop_id;

    $.getJSON("config_separarCuentas.php", send, function (datos) {
        if (datos !== null) {
        if (datos.str > 0) {

            var verificarPlu = {"verificarPlu": 1};
            send = verificarPlu;
            send.odp_id = $("#hide_odp_id").val();
            send.dop_id = dop_id;
            send.mesa_id = mesa;
            send.plu_id = datos[0]["plu_id"];
            send.dop_cuenta = dop_cuenta;
            send.cantidad_plus = $("#cantidadOK").val();
            $.getJSON("config_separarCuentas.php", send, function (datos) {
                if (datos.str > 0) {
                    var plu_id = datos[0]['plu_id'];
                    var new_dop_id = datos[0]['dop_id'];
                    var old_dop_id = dop_id;
                    fn_incrementarPlu(new_dop_id, plu_id, old_dop_id);
                } else {
                    fn_agregarPlu(dop_id, dop_cuenta);
                }
            });
        } else {
            alertify.alert('No existe el plu la cuenta');
        }
        }
    });
    $("#mdl_rdn_pdd_crgnd").hide();
}
/*----------------------------------------------------------------------------------------------------
 Función que incrementa la cantidad del plu que ya existe en una cuenta
 -----------------------------------------------------------------------------------------------------*/
function fn_incrementarPlu(dop_id, plu_id, old_dop_id) {

    var send;
    var verificarCantidadPlu = {"verificarCantidadPlu": 1};
    send = verificarCantidadPlu;
    send.odp_id = document.getElementById("hide_odp_id").value;
    send.plu_id = plu_id;
    send.dop_id = dop_id;

    $.getJSON("config_separarCuentas.php", send, function (datos) {

        if (datos.str > 0) {
            var incrementarPlu = {"incrementarPlu": 1};
            send = incrementarPlu;
            send.dop_id = datos[0]["dop_id"];
            send.odp_id = datos[0]["odp_id"];
            send.plu_id = datos[0]["plu_id"];
            send.dop_cantidad = (parseFloat(datos[0]["dop_cantidad"]) + 1);
            send.old_dop_id = old_dop_id;
            $.getJSON("config_separarCuentas.php", send, function (datos) {
                if (datos.str > 0) {
                    fn_actualizarCuenta();
                } else {
                    alertify.alert('No se pudo agregar el plu a la cuenta');
                }
            });
        } else {
            alertify.alert('No se pudo agregar el plu a la cuenta');
        }
    });
}
/*----------------------------------------------------------------------------------------------------
Función que agrega un plu si no existe y verifica la cantidad del plu y si es mayor a 1 realiza proceso
-----------------------------------------------------------------------------------------------------*/
function fn_agregarPlu(dop_id, new_dop_cuenta) {

    var send;
    var verificarCantidad = {"verificarCantidad": 1};
    send = verificarCantidad;
    send.odp_id = document.getElementById("hide_odp_id").value;
    send.dop_id = dop_id;
    $.getJSON("config_separarCuentas.php", send, function (datos) {
        if (datos.str > 0) {
            var agregarPlu = {"agregarPlu": 1};
            send = agregarPlu;
            send.dop_id = datos[0]["dop_id"];
            send.odp_id = datos[0]["odp_id"];
            send.plu_id = datos[0]["plu_id"];
            send.dop_cantidad = (parseFloat(datos[0]["dop_cantidad"]) - 1);
            send.dop_iva = datos[0]["dop_iva"];
            send.dop_precio_unitario = datos[0]["dop_precio_unitario"];
            send.dop_total = datos[0]["dop_total"];
            send.dop_cuenta = datos[0]["dop_cuenta"];
            send.dop_estado = datos[0]["dop_estado"];
            send.new_dop_cuenta = new_dop_cuenta;
            $.getJSON("config_separarCuentas.php", send, function (datos) {
                if (datos.str > 0) {
                    fn_actualizarCuenta();
                } else {

                    alertify.alert('No se pudo agregar el plu a la cuenta.');
                }
            });
        } else {
            fn_actualizarPlu(dop_id, new_dop_cuenta);
        }
    });
}
/*----------------------------------------------------------------------------------------------------
 Función que actualiza el dop_cuenta del plu con el id obtenido
 -----------------------------------------------------------------------------------------------------*/
function fn_actualizarPlu(dop_id, dop_cuenta) {
    var send;
    var actualizarPlu = {"actualizarPlu": 1};
    send = actualizarPlu;
    send.odp_id = document.getElementById("hide_odp_id").value;
    send.dop_cuenta = dop_cuenta;
    send.dop_id = dop_id;
    $.getJSON("config_separarCuentas.php", send, function (datos) {
        if (datos.str > 0) {
            fn_actualizarCuenta();
        } else {
            regresarProductoaCuenta(dop_id);

        }
    });
}
/*----------------------------------------------------------------------------------------------------
Función que actualiza el detalle de las cuentas
-----------------------------------------------------------------------------------------------------*/
function fn_actualizarCuenta() {
    var idc = acum_split_min;
    var send;
    var actualizarCuenta = {"actualizarCuenta": 1};
    for (idc; idc <= acum_split_max; idc++) {
        document.getElementById("hide_odp_id").value;
        send = actualizarCuenta;
        send.odp_id = document.getElementById("hide_odp_id").value;
        send.dop_cuenta = idc;
        $.ajax({
            async: false, url: "config_separarCuentas.php", data: send, dataType: "json", success: function (datos) {
                $(".listado ul#id" + idc + "").empty();
                $(".subTotal_id" + idc + "").empty();
                $(".baseCero_id" + idc + "").empty();
                $(".baseDoce_id" + idc + "").empty();
                $(".Iva_id" + idc + "").empty();
                $(".Total_id" + idc + "").empty();
                var subTotalFinal = 0;
                var basedoceFinal = 0;
                var baseceroFinal = 0;
                var IvaFinal = 0;
                var TotalFinal = 0;
                var html = "";
                for (var i = 0; i < datos.str; i++) {
                    html = "<li id=" + datos[i]["dop_id"] + " onclick=\"fn_seleccionarItem('" + datos[i]["ancestro"] + "' ," + idc + "   )\"  id_detalle_padre='"+datos[i]['IDDetalleOrdenPedidoPadre']+"' ancestro=" + datos[i]["ancestro"] + " data-id=" + datos[i]["dop_id"] + "><div class='listaproductosCant'>" + datos[i]["dop_cantidad"] + "</div><div class='listaproductosDesc'>" + datos[i]["magp_desc_impresion"] + "</div><div class='listaproductosVal'>$" + (datos[i]["dop_total"] * datos[i]["dop_cantidad"]).toFixed(2) + "</div></li>";
                    $(".listado ul#id" + idc + "").append(html);
                }
                fn_listaPendiente_impuesto(idc);
                noSelectBeneficio();
                fn_GuardarObtenerVariables(idc, 1);
//                var send1;
//                send1 = {"listaPendiente_impuesto": 1};
//                send1.odp_id = send.odp_id;
//                send1.idc = idc;
//                $.ajax({async: false, url: "config_separarCuentas.php", data: send1, dataType: "json", success: function (data1) {
//                        if (data1.str > 0) {
//                            $("#subTotal_id" + idc).html(data1[0]['SUBTOTAL'].toFixed(2));
//                            $("#Iva_id" + idc).html(data1[0]['IVA'].toFixed(2));
//                            $("#Total_id" + idc).html(data1[0]['TOTAL'].toFixed(2));
//                        }
//                    }
//                });
            }
        });
    }
}

/*----------------------------------------------------------------------------------------------------
 Función que factura la cuenta seleccionada, de acuerdo al id
 -----------------------------------------------------------------------------------------------------*/
function fn_facturarCuenta(dop_cuenta) {
    var odp_id = document.getElementById("hide_odp_id").value;
    var mesa_id = document.getElementById("hide_mesa_id").value;

    localStorage.setItem("ls_odp_id", odp_id);
    localStorage.setItem("ls_dop_cuenta", dop_cuenta);                   
    localStorage.setItem("ls_mesa_id", mesa_id);
    localStorage.setItem("ls_recupera_orden", 0);
    localStorage.setItem("cuenta", dop_cuenta);
    
    if (!($(".listado ul#id" + dop_cuenta + " li").length)) {
        alertify.alert('No ha seleccionado un producto para cobrar');
    } else {

        fn_obtieneParametrosVoucher(dop_cuenta); // Obtener parametros en caso de que sea voucher.

        $("#formCobrar").html('<form action="../facturacion/factura.php" name="cobro" method="post" style="display:none;"><input type="text" name="odp_id" value="' + odp_id + '" /><input type="text" name="dop_cuenta" value="' + dop_cuenta + '" /><input type="text" name="mesa_id" value="' + mesa_id + '" />   ' + getUrlParametrosVoucher() + '</form>');
        document.forms["cobro"].submit();
    }

}

var urlParametrosVoucher;
function getUrlParametrosVoucher() {
    return urlParametrosVoucher;
}
function setUrlParametrosVoucher(value) {
    urlParametrosVoucher = value;
}
function fn_obtieneParametrosVoucher(numeroCuenta) {
    var send = {"obtieneParametrosVoucher": 1};
    send.odp_id = document.getElementById("hide_odp_id").value;
    send.dop_cuenta = numeroCuenta;

    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "config_separarCuentas.php", data: send,
        success: function (datos)
        {
            if (datos.str > 0)
            {
                setUrlParametrosVoucher(datos[0]['parametros']);
            } else
            {
                setUrlParametrosVoucher('');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }});
}


var esFS = 0;
function esFullService() {

    esFS = 0;
    var send = {"esFullService": 1};
    send.rst_id = $("#hide_rst_id").val();
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "config_separarCuentas.php", data: send,
        success: function (datos)
        {
            if (datos.str > 0)
            {
                if (datos[0]['respuesta'] === "Si") {
                    esFS = 1;
                } else {
                    esFS = 0;
                }
            } else
            {
                esFS = 0;
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }});
}

/*----------------------------------------------------------------------------------------------------
 Función que regresa a la página de mesas
 -----------------------------------------------------------------------------------------------------*/
function fn_regresar(mesa_id) {
    esFullService();
    if (esFS === 0) {
        alertify.error('No permitido en Fast Food');
    } else {
        //        if (cuentaAfectada.split('_')[0] === "-1") {
        // window.location.replace("../ordenpedido/userMesas.php");
        var aux = '';
        //window.location.href = "../ordenpedido/userMesas.php";
        send = {"obtenerDatosRegresarG": 1};
        send.mesa_id = mesa_id;
        send.rst_id = $('#hide_rst_id').val();
        send.IDUserPos = $('#hide_usr_id').val();

        $.ajax({async: false, type: "GET", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "config_ordenPedido.php", data: send
            , beforeSend: function (datos) {
                var numeroCuentas = localStorage.getItem("numero_cuentas");
                for (var i = 1; i <= numeroCuentas; i++) {
                    fn_GuardarObtenerVariables(i, 2);
                }
            }
            , success: function (datos) {
                if (datos.str > 0) {
                    aux = "?IDPisos=" + datos[0]["IDPisos"] + "&IDAreaPiso=" + datos[0]["IDAreaPiso"];
                    window.location.href = "../ordenpedido/userMesas.php" + aux;
                }
            }
            , complete: function (data) {
                //for (acum_split_min; acum_split_min <= acum_split_max; acum_split_min++) {
                fn_imprimirOrdenPedidoTodas(1);
                //}
            }
        });
        //        } else {
        //            alertify.alert('Cobre primero el Voucher de la cuenta ' + cuentaAfectada.split('_')[0]);
        //        }
    }
}

function fn_imprimirOrdenPedidoTodas(i) {
    var odp_id = $("#hide_odp_id").val();
    var est_ipd = $("#hide_est_ip").val();
    var usr_id = $("#hide_usr_id").val();
    var rst_id = $("#hide_rst_id").val();
    send = {"impresionOrdenPedidoTodas": 1};
    send.odp_id = odp_id;
    send.est_ipd = est_ipd;
    send.usr_id = usr_id;
    send.rst_id = rst_id;
    send.dop_cuenta = 0;
    send.todas = i;
    $.getJSON("config_ordenPedido.php", send, function (datos) {
        if (datos.str > 0) {
            if (datos[0]['Confirmar'] < 1) {
                alertify.alert('No existen impresiones pendientes');
            }
        }
    });
}


//recuperar cuenta
function  fn_recuperar_cuenta_split(numSplit) {
    localStorage.setItem('cuenta', numSplit);
    localStorage.setItem('tipo', 'cuenta_separada');
    mesa_id = $("#hide_mesa_id").val();
    window.location.href = "tomaPedido.php?numMesa=" + mesa_id + "&numSplit=" + numSplit;
}


function dividirProductos() {
    dividir_producto = true;
    if ($('#split_dividir').html() === 'Finalizar División') {
        if (cantidadSeleccionados > 1) {
            finalizarSplits();
            alertify.success('Actualización completa.');
        } else {
            alertify.alert('Seleccione más de una cuenta.');
        }
    } else {
        $(".listadoPedido li").removeClass("focus");
        $('#split_dividir').html('Seleccione el producto');
        $('#split_dividir').addClass('paso1');
        $('#split_dividir').removeClass('facturarCuenta');
        $('#cancelar_dividir').show(100);
        alertify.success('Seleccione el producto');
    }
}

function seleccionarContenedor(id) {

    if (dividir_producto) {

        if (hay_producto_seleccionado) {

            if ($('#' + id).attr("class") === 'contenedorIzquierda' /*|| $('#' + id).attr("class") === 'contenedorIzquierda cuenta_afectada' */) {
                $('#' + id).addClass('contenedor_active');
                cantidadSeleccionados++;
            } else {
                $('#' + id).removeClass('contenedor_active');
                cantidadSeleccionados--;
            }
            if (cantidadSeleccionados === 0) {
                cancelar_split_productos();
            }
        }
    } else {
        if (val !== null && dividir_producto === false) {
            var split = id.substring(2, id.length);
            fn_verificarPlu(val[0], split);
            val = [];
            cambiarplu = 0;
        }
    }
}



function finalizarSplits() {

    var odp_id = $('#hide_odp_id').val();
    var tem_splits = '';
    for (var i = 1; i <= cant_split; i++) {
        if ($('#ct' + i).attr('class') === 'contenedorIzquierda contenedor_active') {
            tem_splits += '(' + i + '),';
        }
    }
    tem_splits = tem_splits.substring(0, tem_splits.lastIndexOf(","));
    //alert('El detalle ' + dop_id_seleeccionado + ' Se dividio en los items' + tem_splits + ' y el primer split es ' +primer_split);
    send = {"finalizarSplits": 1};
    send.odp_id = odp_id;
    send.splits = tem_splits;
    send.dop_id = dop_id_seleeccionado;
    send.dop_cuenta = primer_split;
    send.mesa_id = $('#hide_mesa_id').val();

    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "config_separarCuentas.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                window.location.replace(".." + datos[0]['url_cuenta']);
            } else {
                alertify.error("Ocurrió un error.");
                return false;
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
    //  */

}

function cancelar_split_productos() {

    dividir_producto = false;
    hay_producto_seleccionado = false;
    dop_id_seleeccionado = '';
    primer_split = 0;
    primer_splitBoolean = true;

    $('#split_dividir').addClass('facturarCuenta');
    $('#split_dividir').removeClass('paso1');
    $('#split_dividir').html('Dividir Productos');
    $(".listadoPedido li").removeClass("focus");
    for (var i = 1; i <= acum_split_max; i++) {
        $('#ct' + i).removeClass('contenedor_active');
    }
    $('#cancelar_dividir').hide(100);

}

var eliminados = 1;
function  clickIzquierda() {
    if ((cant_split - eliminados) > 4) {
        if ((cant_split - eliminados) > 1) {
            $('#ct' + vector[eliminados]).hide(100);
            eliminados++;
        }
    }
}
function  clickDerecha() {
    if (eliminados > 1) {
        eliminados--;
        $('#ct' + vector[eliminados]).show(100);
    }
}


function existe(elemento, arreglo) {
    for (var i = 0; i < arreglo.length; i++) {
        if (arreglo[i] === elemento) {
            return true;
        }
    }
    return false;
}
var vector = [];
var cuentaAfectada = "-1_-1";
function fn_split_cuenta(split) {
    if (Number.isInteger(split)) {
        var onclickVoucher = "";
        var styleCuentaAfectada = "";
        cuentaAfectada = "-1_-1";

        if (!document.getElementById('txt_cuentaAfectada')) {
            styleCuentaAfectada = "";
        } else {
            cuentaAfectada = $("#txt_cuentaAfectada").val();
            onclickVoucher = "  onclick=\" fn_FacturarConCupon(" + split + " , " + $("#txt_montoCupon").val() + "  )\" ";
            styleCuentaAfectada = ((cuentaAfectada.split('_')[0] == split) ? " cuenta_afectada" : "");

            if (styleCuentaAfectada == "") {
                styleCuentaAfectada = ((cuentaAfectada.split('_')[1] == split) ? " cuenta_afectada" : "");
            }
        }

        vector[cantidadDeDiviciones] = split;

        var cssStyle = "  ";
        if (cant_split === 1) {
            cssStyle = "  style=\"margin-left: 5px;\"   ";
        }

        content_split = "<div  " + cssStyle + "  onclick='seleccionarContenedor(\"ct" + split + "\")'  id=\"ct" + split + "\" class=\"contenedorIzquierda" + styleCuentaAfectada + "\">\n" +
                "                <div class= \"content_split\">   \n" +
                "                    <div class=\"header_split facturarCuenta  " + ((existe('Recuperar Cuenta', listaPermisos) === true) ? '' : 'classDisabled') + "  \" style=\"background-color: rgba(41, 150, 47, .8)\"  " + ((existe('Recuperar Cuenta', listaPermisos) === true) ? "onclick= \" fn_recuperar_cuenta_split(" + split + ")\"" : '') + "  >   \n" +
                "                        <label>Recuperar cuenta <br> #" + split + " </label> \n" +
                "                    </div>\n" +
                "                </div>     \n" +
                "            <div class=\"MasterListaPedido\"  id=\"masterListadoPedido" + split + "\">   \n\
                 <div class=\"listaPedido\"      >\n" +
                "                    <div class=\"listado\">\n" +
                "                        <ul id=\"id" + split + "\" class=\"listadoPedido\">\n" +
                "                        </ul>\n" +
                "                    </div>\n" +
                "                </div>   </div>    \n" +
                "                <div class=\"botonesLista\" >\n" +
                "                  <button id=\"btn_facturarCuenta\"   " + ((existe('Cobrar', listaPermisos) === true) ? ' class=\"facturarCuenta permisoCobrar\"' : 'disabled=\"disabled\" class=\"facturarCuenta classDisabled\"') + "   " + ("onclick=\" fn_facturarCuenta(" + split + ")\"") + "   title=\"Facturar Cuenta\">Cobrar</button>\n" +
                "                  <button id=\"btn_imprimirCuenta\"   " + ((existe('Imprimir Pre-Cuenta', listaPermisos) === true) ? ' class=\"facturarCuenta\"' : 'disabled=\"disabled\" class=\"facturarCuenta classDisabled\"') + "   onclick=\"fn_ImprimirPrecuentasSeparadas(" + split + ")\" title=\"Imprimir Cuenta\">Pre Cuenta</button>\n" +
                "                </div>\n" +
                "                <div class=\"calculosLista\">\n" +
                "                    <table class=\"calculo\" border=\"0\" cellpadding=\"1\" cellspacing=\"0\">\n" +
                "                        <tr>\n" +
                "                            <td width=\"300\" align=\"right\">Subtotal: </td>\n" +
                "                            <td width=\"20\"></td>\n" +
                "                            <td align=\"right\" id=\"subTotal_id" + split + "\"></td>\n" +
                "                            <td width=\"30\"></td>\n" +
                "                        </tr>\n" +
                "                        <tr>\n" +
                "                            <td width=\"300\" align=\"right\">Iva : </td>\n" +
                "                            <td width=\"20\"></td>\n" +
                "                            <td align=\"right\" id=\"Iva_id" + split + "\"></td>\n" +
                "                            <td width=\"30\"></td>\n" +
                "                        </tr>\n" +
                "                        <tr >\n" +
                "                            <td width=\"300\" align=\"right\"></td>\n" +
                "                            <td width=\"20\"></td>\n" +
                "                            <td style=\"border-top:2px dashed #000; height:5px;\"></td>\n" +
                "                            <td width=\"30\"></td>\n" +
                "                        </tr>\n" +
                "                        <tr>\n" +
                "                            <td width=\"300\" align=\"right\">Total: </td>\n" +
                "                            <td width=\"20\"></td>\n" +
                "                            <td align=\"right\" id=\"Total_id" + split + "\"></td>\n" +
                "                            <td width=\"30\"></td>\n" +
                "                        </tr>\n" +
                "                    </table>\n" +
                "               </div>\n"+
                "                <div class=\"DatosPersonales\">\n" +
                "                    <table class=\"calculo\" border=\"0\" cellpadding=\"1\" cellspacing=\"0\">\n" +
                "                        <tr>\n" +
                "                            <td>Datos Personales</td>\n" +
                "                        </tr>\n" +
                "                        <tr class=\"datos_personales\" id=\"datos_personales_"+split+"\">\n" +
                "                        </tr>\n" +
                "                    </table>\n" +
                "                </div>";


        if (cant_split >= 4) {
            var ancho = $("#contenedor_split").width() + 512;
            $("#contenedor_split").width(ancho);

            $('#imgNext').show(100);
            $('#imgBack').show(100);
        }
        cant_split = cant_split + 1;
        $("#contenedor_split").append(content_split);

        localStorage.setItem('numero_cuentas', split);
        localStorage.setItem('cuenta', split);

        $('#numeroLista' + cant_split).shortscroll();


        if ((cant_split - eliminados) > 4) {


            if ((cant_split - eliminados) > 1) {

                $('#ct' + vector[eliminados]).hide(100);
                eliminados++;
            }
        }

        fn_VisualizaBoton();
        fn_obtenerID();

        var cuenta = localStorage.getItem('cuenta');
        fn_GuardarObtenerVariables('', 0);
        addDatosPersonale(cuenta);
    }
}

//imprime todas las precuentas que existen en el split
//function fn_ImprimirTodasPrecuenta() {
//    var ordenes = null;
//    var send_num;
//    var numeroCuentas = {"numeroCuentas": 1, "odp_id": $("#hide_odp_id").val()};
//    send_num = numeroCuentas;
//    $.ajax({async: false, url: "config_separarCuentas.php", data: send_num, dataType: "json", success: function (num) {
//            if (num.str > 0) {
//                ordenes = num[0]["acum_split"];
//            }
//        }});
//    for (var i = 1; i <= ordenes; i++) {
//        fn_imprimirPreCuenta(i);
//    }
//}
//imprime precuenta en el boton imprimi
//@rst_id as int, 
//  @odp_id varchar(40),
//        @usr_id varchar(40),
//        @num_cuenta int,
//        @est_id varchar(40),
//        @prd_id varchar(40),
//        @ctrc_id varchar(40)


function  fn_recuperar_cuenta_dividida(odp_id) {


    if (estado_divide) {

        var send;
        var nom_cuenta = {"recuperar_cuenta_dividida": 1};
        send = nom_cuenta;
        send.odp_id = odp_id;

        $.ajax({async: false, type: "GET", url: "config_separarCuentas.php", data: send, dataType: "json", success: function (data) {
            if (data.str > 0) {
                    if (data[0]['opcion'] == 1) {
                        URL = document.URL;
//                        if (URL.indexOf('recuperar=1') != -1)
//                            URL = URL.replace('recuperar=1', 'recuperar=0');
//                        else
//                            URL = URL.replace('recuperar=0', 'recuperar=1');

                        window.location = URL;

                    }
                    if (data[0]['opcion'] == 2) {

                        var numSplit = 1;
                        mesa_id = $("#hide_mesa_id").val();
                        window.location.href = "tomaPedido.php?numMesa=" + mesa_id + "&numSplit=" + numSplit;
                    }
                }
            }
        });
        estado_divide = false;
    } else {
        fn_validarUsuarioAdministrador1(odp_id);
    }
}


function fn_validarCredencialesAdministrador1(odp_id) {
    var usr_clave = $("#usr_clave").val();
    var cfac_id = $('#listadoPedido').find("li.focus").attr("id");
    var rst_id = document.getElementById("hide_rst_id").value;
    if (usr_clave.indexOf('%') >= 0) {
        var old_usr_clave = usr_clave.split('?;')[0];
        var new_usr_clave = old_usr_clave.replace(new RegExp("%", 'g'), "");
        var usr_tarjeta = new_usr_clave;
        usr_clave = 'noclave';
    } else {
        var usr_tarjeta = 0;
    }
    if (usr_clave != "") {
        send = {"validarUsuario": 1};
        send.usr_clave = usr_clave;
        send.usr_tarjeta = usr_tarjeta;
        $.getJSON("config_ordenPedido.php", send, function (datos) {
            if (datos.str > 0) {
                $("#anulacionesContenedor").dialog("close");
                $("#usr_clave").val("");
                estado_divide = true;
                fn_recuperar_cuenta_dividida(odp_id);

            } else {
                fn_numerico("#usr_clave");
                alertify.confirm("La clave ingresada no tiene permisos de Administrador.", function (e) {
                    if (e) {
                        alertify.set({buttonFocus: "none"});
                        $("#usr_clave").focus();
                    }
                });
                $("#usr_clave").val("");
            }
        });
    } else {
        fn_nubtnVirtualOKpqmerico("#usr_clave");
        alertify.confirm("Ingrese clave de Administrador", function (e) {
            if (e) {
                alertify.set({buttonFocus: "none"});
                $("#usr_clave").focus();
            }
        });
        $("#usr_clave").val("");
    }
}

function fn_validarUsuarioAdministrador1(odp_id) {
    $("#anulacionesContenedor").show();
    $("#anulacionesContenedor").dialog({
        modal: true,
        width: 500,
        heigth: 500,
        resize: false,
        opacity: 0,
        show: "none",
        hide: "none",
        duration: 500,
        open: function (event, ui) {
            $('.ui-dialog-titlebar').hide();
            fn_numerico('#usr_clave');
            $('#usr_clave').val('');
            $('#usr_clave').attr('onchange', 'fn_validarCredencialesAdministrador1("' + odp_id + '")');
        }
    });
}

function addDatosPersonale(cuenta_numero){
    for (var i = 1; i <= cuenta_numero; i++) {
        $('#datos_personales_'+i).html('');
        var nombre = localStorage.getItem('ls_nombres_'+i);
        var cedula = localStorage.getItem('ls_documento_'+i);
        var telefono = localStorage.getItem('ls_telefono_'+i);
        var correo = localStorage.getItem('ls_correo_'+i);
        var html = '';
        if ((nombre !== '') && (cedula !== '') && (telefono !== '') && (correo !== '') && (nombre !== null) && (cedula !== null) && (telefono !== null) && (correo !== null)){
            html += '<td>CLIENTE: '+nombre+'</td>';
            html += '<td>TELEFONO: '+telefono+'</td>';
            $('#datos_personales_'+i).html(html);
        }
    }
}

function noSelectBeneficio(){
    var cuentas = localStorage.getItem('numero_cuentas');
    for (var i = 0; i <= cuentas; i++) {
        var id = localStorage.getItem('dop_beneficio_'+i);
        if ((id !== null) && (id !== '')){
            $('#'+id).css('pointer-events', 'none');
        }
    }
}