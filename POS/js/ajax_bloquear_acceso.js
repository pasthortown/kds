///////////////////////////////////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Daniel Llerena ///////////////////////////////////////////////////
///////DESCRIPCION: Bloqueo de menu y botones si el ingreso es de un Administrador ////////
///////TABLAS INVOLUCRADAS:  //////////////////////////////////////////////////////////////
///////FECHA CREACION: 23/11/2015  ////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////// ///////////////////////////////
///////FECHA ULTIMA MODIFICACION: 07/07/2016 //////////////////////////////////////////////
///////USUARIO QUE MODIFICO:  Christian Pinto /////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Cierre Periodo abierto mas de un día /////////////////////
///////////////////////////////////////////////////////////////////////////////////////////

$(document).ready(function ()
{
    cargaPedidoRapido();
    fn_LoadAccess($('#hide_usr_id').val(), $('#pantallaAcceso').val());
    fn_mostrarBotonCobrarEnEstacionTomaPedido();
    fn_VisualizaBoton();
//    if ($("#banderaCierrePeriodo").val() === 'FinDeDiaCierrePeriodo')
//    {
//        //$("#nuevaorden").prop('disabled', true);
//        //$('#nuevaorden').css('display', 'none');
//        fn_bloquearaccesoCierrePerido();
//    }
//    else
//    {
//        fn_bloquearacceso();
//    }
//    

});

var MOSTRAR_BOTON = false;
function fn_mostrarBotonCobrarEnEstacionTomaPedido() {
    MOSTRAR_BOTON = false;
    var send = {"mostrarBotonCobrarEnEstacionTomaPedido": 1};
    send.IDEstacion = $("#hide_est_id").val();
    send.rst_id = $("#hide_rst_id").val();
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "config_ordenPedido.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                if (datos[0].mostrarBoton == 1) {
                    MOSTRAR_BOTON = true;
                }
            }
        }
        , error: function (e) {
            console.log(e);
        }

    });

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
                    if (!MOSTRAR_BOTON){
                        $("#cobrar").hide();
                        $("#cobrarVoucher").hide();
                        $("#btn_facturarCuenta").hide();
                    }
                   
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
var estado = "";
function SetesPedidorapido(est) {
    estado = est;
}
function getesPedidorapido() {
    return estado;
}


function cargaPedidoRapido() {
    // hide_mesa_id   
    send = {"obtieneMesaPredeterminada": 1};
    send.est_id = $('#hide_est_id').val();
    send.cdn_id = $('#hide_cdn_id').val();
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../seguridades/config_usuario.php", data: send,
        success: function (datos)
        {
            if (datos.str > 0)
            {
                if ($('#hide_mesa_id').val() === datos[0]['IDMesa']) {
                    SetesPedidorapido("Si");
                } else
                {
                    SetesPedidorapido("No");
                }
            } else
            {
                SetesPedidorapido("No");
            }
        }});
}



var listaPermisos = new Array();
function fn_LoadAccess(usuario_id, pantalla) {


    var tipoServicio = $('#txtTipoServicio').val();

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



                        if ($('#' + IDBoton).attr('servicio') === tipoServicio || $('#' + IDBoton).attr('servicio') === undefined) {

                            $('#' + IDBoton).removeAttr('disabled');
                            $('#' + IDBoton).removeClass('classDisabled');

                            if ($('#' + IDBoton).attr('class') === 'boton_Opcion_Bloqueado') {

                                $('#' + IDBoton).removeClass('boton_Opcion_Bloqueado');
                                $('#' + IDBoton).addClass('boton_Opcion');
                                $('#' + IDBoton).show();


                            } else if ($('#' + IDBoton).attr('class') === 'boton_Accion_Bloqueado') {
                                $('#' + IDBoton).removeClass('boton_Accion_Bloqueado');
                                $('#' + IDBoton).addClass('boton_Accion');

                            }
                        }// Fin valida tipo servicio.


//                        }// Fin valida que exista valor en el atributo servicio.

                    }// Fin valida que exista valor en el atributo name.
                    listaPermisos[i] = datos[i]['descripcion'];
                }// fin For


                if (getesPedidorapido() === "Si" && tipoServicio === "2") {

                    $('#separarCuentas').removeClass('boton_Opcion');
                    $('#separarCuentas').addClass('boton_Opcion_Bloqueado');
                    $('#separarCuentas').hide();

                    $('#dividirCuenta').removeClass('boton_Opcion');
                    $('#dividirCuenta').addClass('boton_Opcion_Bloqueado');
                    $('#dividirCuenta').hide();

                }


                if (!existe('Panel producto', listaPermisos)) {
                    $('#barraProducto').addClass('contenedor_productos_disabled');
                }
                if (!existe('Panel categoria', listaPermisos)) {
                    $('#barraCategoria').addClass('contenedor_productos_disabled');
                }




            } else
            {
                alertify.error('No cuenta con ningún permiso sobre ésta pantalla.');
            }
        }});



}


function existe(elemento, arreglo) {
    for (var i = 0; i < arreglo.length; i++) {
        if (arreglo[i] === elemento) {
            return true;
        }
    }
    return false;
}

function fn_bloquearacceso()
{
    var bloquear = $("#bloqueo").val();

    if (bloquear == 1)
    {
        //CATEGORIA Y PRODUCTOS DESHABILITADOS
        $('#barraCategoria').addClass('contenedor_productos_disabled');
        $('#barraProducto').addClass('contenedor_productos_disabled');
        $('#cntdr_mn_dnmc_stcn').addClass('contenedor_productos_disabled');

        //BOTONES DE ACCION DESHABILITADOS
        $('#cobrar').addClass('boton_accion_disabled');
        $('#agregarCantidad').addClass('boton_accion_disabled');
        $('#btn_eliminarElemnto').addClass('boton_accion_disabled');
        $('#comentar').addClass('boton_accion_disabled');
        $('#btn_sistema').addClass('boton_accion_disabled');

        //BOTONES DEL MENU DESPLEGABLE DESHABILITADO
        $('#separarCuentas').addClass('boton_accion_disabled');
        /*  $('#precuenta').addClass('boton_accion_disabled');	
         $('#imprimir_orden').addClass('boton_accion_disabled');	
         $('#buscar').addClass('boton_accion_disabled');
         */
    } else {
        //alert('Cajero');
    }
}

function fn_bloquearaccesoCierrePerido()
{

    //CATEGORIA Y PRODUCTOS DESHABILITADOS
    $('#barraCategoria').addClass('contenedor_productos_disabled');
    $('#barraProducto').addClass('contenedor_productos_disabled');
    $('#cntdr_mn_dnmc_stcn').addClass('contenedor_productos_disabled');

    //BOTONES DE ACCION DESHABILITADOS
    //$('#cobrar').addClass('boton_accion_disabled');
    $('#agregarCantidad').addClass('boton_accion_disabled');
    //$('#btn_eliminarElemnto').addClass('boton_accion_disabled');
    $('#comentar').addClass('boton_accion_disabled');
    $('#btn_sistema').addClass('boton_accion_disabled');

    //BOTONES DEL MENU DESPLEGABLE DESHABILITADO
    /*$('#separarCuentas').addClass('boton_accion_disabled');	
     $('#precuenta').addClass('boton_accion_disabled');	
     $('#imprimir_orden').addClass('boton_accion_disabled');*/
    $('#btn_transacciones').addClass('boton_accion_disabled');
    $('#btn_cuponesSG').addClass('boton_accion_disabled');

}