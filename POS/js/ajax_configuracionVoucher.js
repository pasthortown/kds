/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var table;
var array;
var num_;
var arrayValor = [];
var _lastIndex = 0;
$(document).ready(function () {

    fn_traerListaVoucherAerolineas();

    table = $('#listadoVoucher').DataTable();
    $('#listadoVoucher tbody').on('click', 'td.details-control', function () {

        var tr = $(this).closest('tr');
        var row = table.row(tr);
        var idTR = ($(this).closest('tr').attr('id'));

        var ID_ColeccionCadena = (idTR.split('|')[0]);
        var ID_ColeccionDeDatosCadena = (idTR.split('|')[1]);

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            var trn = $('#listadoVoucher tbody .shown').closest('tr');
            var rown = table.row(trn);
            
            rown.child.hide();
            trn.removeClass('shown');
            
            row.child(format(ID_ColeccionCadena, ID_ColeccionDeDatosCadena)).show();
            tr.addClass('shown');
            
        }
    });

    $("#search-box").keyup(function () {
        var send;
        send = {};
        send.metodo = "buscaClienteAx";
        send.documento = $("#search-box").val();
        send.tipoDocumento = $('input[name=optradio]:checked').val();
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            Accept: "application/json, text/javascript2",
            contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
            url: "../../facturacion/clienteWSClientes.php",
            data: send,
            beforeSend: function () {
                $("#search-box").css("background", "#FFF url('../../imagenes/LoaderIcon.gif') no-repeat right");
            },
            success: function (datos)
            {
                $("#search").empty();
                if (jQuery.isEmptyObject(datos))
                {
                    $("#search-box").css("background", "#FFF");
                }

                if (datos[0].estado === 1)
                {
                    for (i in datos[0].cliente) {
                        $("#suggesstion-box").show();
                        $("#search").append(
                                '<option value="' +
                                datos[0].cliente[i].descripcion +
                                '"></option>'
                                );
                    }
                    array = datos[0].cliente;
                    $("#search-box").css("background", "#FFF");
                } else {
                    fn_setearModalMonto();
                    $("#search-box").css("background", "#FFF");
                }
                if (datos[0].estado === -1) {
                    fn_setearModalMonto();
                    $("#search-box").css("background", "#FFF");
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alertify.alert("ERROR AL CONSULTAR LA INFORMACION. " + jqXHR + ': ' + textStatus + ': ' + errorThrown);
                fn_setearModalMonto();
            }
        }); // fin de peticion
    });

});

function format(ID_ColeccionCadena, ID_ColeccionDeDatosCadena) {
    arrayValor = [];
    _lastIndex = 0;
    var aux = "";
    var send;
    send = {"buscarMontoVoucher": 1};
    send.opcion = 2;
    send.ID_ColeccionCadena = ID_ColeccionCadena;
    send.ID_ColeccionDeDatosCadena = ID_ColeccionDeDatosCadena;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        Accept: "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../../mantenimiento/adminCadena/configAdminCadena.php",
        data: send,
        success: function (data) {
            if (data.str > 0) {
                if (data[0].valor !== '' && data[0].valor !== '0') {
                    arrayValor = data[0].valor.split('|');

                    $.each(arrayValor, function (index, value) {
                        //alert(index + ':' + value);
                        aux += '<tr id="' + index + '">' +
                                '<td>Valor:</td>' +
                                '<td> <input type="text" class="form-control" value="' + value + '" disabled/> </td>' +
                                '<td><button onclick="fn_eliminarMonto(' + index + ",'" + ID_ColeccionCadena + "'" + ',' + "'" + ID_ColeccionDeDatosCadena + "'" + ')"><img src="../../imagenes/admin_resources/btn_eliminar.png"></button></td>' +
                                '</tr>';
                        _lastIndex = index;
                    });
                }
            }
        }
    });

    return  '<div class="col-xs-6">' +
            '<table id="_' + ID_ColeccionDeDatosCadena + '" class="table .table-striped" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
            '<tr> \n\
            <td>Ingrese un nuevo valor</td>' +
            '<td>\n\
                               <p><input id="' + ID_ColeccionDeDatosCadena + '" name="' + ID_ColeccionCadena + '" type="text" class="soloNumeros form-control" />\n\
                               </p>\n\
                        </td>' +
            '<td><button onclick="fn_guardarMontoVoucher(' + "'" + ID_ColeccionCadena + "'" + ',' + "'" + ID_ColeccionDeDatosCadena + "'" + ')"><img src="../../imagenes/admin_resources/guardar.png"></button><td>' +
            '</tr>' +
            aux +
            '</table>' +
            '</div>';

}
function fn_listarVoucher(id_ccr, datos, isActive) {
    var Activo = isActive ? "Activo" : "Inactivo";
    return '<tr id = "' + id_ccr + '" class="clienteExterno">' +
            '<td class="details-control"></td>' +
            '<td name="' + datos.descripcion + '">' + datos.descripcion + '</td>' +
            '<td name="' + datos.identificacion + '">' + datos.identificacion + '</td>' +
            '<td name="' + datos.direccionDomicilio + '">' + datos.direccionDomicilio + '</td>' +
            '<td name="' + datos.correo + '">' + datos.correo + '</td>' +
            '<td name="' + datos.telefonoDomiclio + '">' + datos.telefonoDomiclio + '</td>' +
            '<td name"status">' + Activo + '</td>' +
            '</tr>';
}

function fn_agregarVoucher() {
    $("#search-box").removeAttr('disabled', 'disabled');
    fn_setearModalMonto();
    $("#btn_guardarCambios").show();
    $("#search-box").css("background", "#FFF");
    $("#modal-container-129396").modal("show");
    $("#activarPolitica").empty();
}

function fn_setearModalMonto() {
    $("#search").empty();
    $("#search-box").val("");
    $("#identificacionExt").val("");
    $("#telefonoExt").val("");
    $("#direccionExt").val("");
    $("#correoExt").val("");
    array = [];
    i = 0;
}

function fn_agregarMonto() {

    $('#valorMonto').val("");
    $("#modal-container-monto").modal("show");
}

function fn_completaDatosCliente(d) {
    $("#identificacionExt").val(d.identificacion);
    $("#telefonoExt").val(d.telefonoDomiclio);
    $("#direccionExt").val(d.direccionDomicilio);
    $("#correoExt").val(d.correo);
}

function onInput() {
    var val = $("#search-box").val();
    var opts = $('#search').children();
    for (var i = 0; i < opts.length; i++) {
        if (opts[i].value === val) {
            fn_completaDatosCliente(array[i]);
            num_ = i;
            break;
        }
    }
}

$("#btn_guardarCambios").click(function () {
    if ($.isEmptyObject(array[num_])) {
        alertify.alert("Existen problemas para guardar este regisrto.");
    } else {
        fn_guardarClienteExternoVoucher();

        var ruta = "/pos/mantenimiento/adminCadena/configuracionVoucher.php";
        $("#incluirPagina").load(ruta);

        $("#modal-container-129396").modal("hide");
        $(".modal-backdrop").fadeOut();
    }
});

function fn_traerListaVoucherAerolineas() {
    var send;
    send = {"traerListaVoucherAerolineas": 1};
    send.opcion = 2;
    send.descripcion = '';
    send.documento = '';
    send.informacionVoucher = '';
    send.valor = 0;

    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        Accept: "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../../mantenimiento/adminCadena/configAdminCadena.php",
        data: send,
        success: function (data) {
            if (data) {
                for (i = 0; i < data.str; i++) {
                    $('#listadoVoucher tbody').append(fn_listarVoucher(data[i].ID_ColeccionCadena + '|' + data[i].ID_ColeccionDeDatosCadena, JSON.parse(data[i].Datos), data[i].isActive));
                }
                $("#listadoVoucher").dataTable({"destroy": true});
            } else {
                alertify.error("Error al consultar los datos.");
            }
        }
    });
}

function fn_guardarClienteExternoVoucher() {
    var send;
    send = {"guardarClienteExternoVoucher": 1};
    send.opcion = 1;
    send.descripcion = array[num_].descripcion;
    send.documento = array[num_].identificacion;
    send.informacionVoucher = array[num_];
    send.valor = 0;


    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        Accept: "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../../mantenimiento/adminCadena/configAdminCadena.php",
        data: send,
        success: function (data) {
            if (data) {
                alertify.alert(data[0].mensaje);
            }
        }
    });
}

function fn_guardarMontoVoucher(ID_ColeccionCadena, ID_ColeccionDeDatosCadena) {
    var valorInput = $.trim($("#" + ID_ColeccionDeDatosCadena).val());
    var validacionInput = fn_validarDecimal(valorInput);

    if (validacionInput) {
        if (fn_validarValores(valorInput, arrayValor)) {
            arrayValor.push(valorInput);
            var send = "";
            var aux = "";
            send = {"guardarMontoVoucher": 1};
            send.opcion = 1;
            send.valor = arrayValor.join('|');//valorInput;
            send.ID_ColeccionCadena = ID_ColeccionCadena;
            send.ID_ColeccionDeDatosCadena = ID_ColeccionDeDatosCadena;
            $.ajax({
                async: false,
                type: "POST",
                dataType: "json",
                Accept: "application/json, text/javascript2",
                contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
                url: "../../mantenimiento/adminCadena/configAdminCadena.php",
                data: send,
                success: function (data) {
                    _lastIndex++;
                    if (data.str > 0) {
                        aux = '<tr id="' + (_lastIndex) + '">' +
                                '<td>Valor:</td>' +
                                '<td> <input type="text" class="form-control" value="' + valorInput + '" disabled/> </td>' +
                                '<td><button onclick="fn_eliminarMonto(' + _lastIndex + ",'" + ID_ColeccionCadena + "'" + ',' + "'" + ID_ColeccionDeDatosCadena + "'" + ')"><img src="../../imagenes/admin_resources/btn_eliminar.png"></button></td>' +
                                '</tr>';
                        $("#_" + ID_ColeccionDeDatosCadena + ' tbody').append(aux);
                        alertify.success(data[0].mensaje);
                        $("#" + ID_ColeccionDeDatosCadena).val('');
                    }
                }

            });
        } else {
            alertify.error("El valor se encuentra registrado");
        }
    } else {
        alertify.error("Ingrese un valor númerico");
    }
}

function fn_validarDecimal(valor) {
    var RE = /^([0-9]+\.?[0-9]{0,2})$/;
    return (RE.test(valor)) ? true : false;
}
function fn_validarValores(value, arr) {
    var status = true;

    for (var i = 0; i < arr.length; i++) {
        var name = arr[i];
        if (parseFloat(name) === parseFloat(value) && parseFloat(name) !== "0") {
            status = false;
            break;
        }
    }
    return status;
}
function fn_eliminarMonto(index, ID_ColeccionCadena, ID_ColeccionDeDatosCadena) {
    //delete arrayValor[index];
    if (index > -1) {
        arrayValor.splice(index, 1);
        if (fn_guardarMovimiento(ID_ColeccionCadena, ID_ColeccionDeDatosCadena)) {
            $("#_" + ID_ColeccionDeDatosCadena + " #" + index).closest('tr').remove();
        }
    }
}
function fn_guardarMovimiento(ID_ColeccionCadena, ID_ColeccionDeDatosCadena) {
    var existe = false;
    var send = "";
    send = {"guardarMontoVoucher": 1};
    send.opcion = 1;
    send.valor = $.trim(arrayValor.join('|'));//valorInput;
    send.ID_ColeccionCadena = ID_ColeccionCadena;
    send.ID_ColeccionDeDatosCadena = ID_ColeccionDeDatosCadena;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        Accept: "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../../mantenimiento/adminCadena/configAdminCadena.php",
        data: send,
        success: function (data) {
            if (data.str > 0) {
                alertify.success(data[0].mensaje);
                existe = true;
            } else {
                existe = false;
            }
        }

    });
    return existe;
}

$(document).on("dblclick", "#listadoVoucher .clienteExterno", function () {
    $("#modal-container-129396").modal("show");

    var d = new Object();
    var valores = [];
    $(this).find("td").each(function (i) {
        valores [i] = $(this).html();
    });

    var arrayIDs = $(this).attr('id').split('|');

    d.identificacion = valores[2];
    d.telefonoDomiclio = valores[5];
    d.direccionDomicilio = valores[3];
    d.correo = valores[4];
    $("#search-box").val(valores[1]);
    $("#search-box").attr('disabled', 'disabled');
    $("#btn_guardarCambios").hide();
    fn_completaDatosCliente(d);

    var valIsActive = valores[6] === "Activo" ? 1 : 0;
    var isActive = valores[6] === "Activo" ? "checked" : "";

    $("#activarPolitica").html('<div class="col-xs-12 col-md-4 pull-right">' +
            '<div class="btn-group">' +
            '<h5 class="text-right"><b>Está Activo?: <input onclick="fn_isActive(' + "'" + arrayIDs[0] + "','" + arrayIDs[1] + "'," + valIsActive + ')" value = "' + valIsActive + '" id="estaActivo" type="checkbox" ' + isActive + '></b></h5>' +
            '</div>' +
            '</div>');

});


//$(document).on("click", ".soloNumeros", function () {
//    var ID_ColeccionCadena = $(this).attr('name');
//    var ID_ColeccionDeDatosCadena = $(this).attr('id');
//    if (ID_ColeccionCadena && ID_ColeccionDeDatosCadena) {
//        fn_cargarArrayVoucher(ID_ColeccionCadena, ID_ColeccionDeDatosCadena);
//    }else{
//        alertify.error("Existe errot al consultar los valores");
//    }
//
//});

function fn_cargarArrayVoucher(ID_ColeccionCadena, ID_ColeccionDeDatosCadena) {
    arrayValor = [];
    _lastIndex = 0;
    var send;
    send = {"buscarMontoVoucher": 1};
    send.opcion = 2;
    send.ID_ColeccionCadena = ID_ColeccionCadena;
    send.ID_ColeccionDeDatosCadena = ID_ColeccionDeDatosCadena;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        Accept: "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../../mantenimiento/adminCadena/configAdminCadena.php",
        data: send,
        success: function (data) {
            if (data.str > 0) {
                if (data[0].valor !== '' && data[0].valor !== '0') {
                    arrayValor = data[0].valor.split('|');
                    $.each(arrayValor, function (index, value) {
                        _lastIndex = index;
                    });
                }
            }
        }
    });
}

function fn_isActive(ID_ColeccionCadena, ID_ColeccionDeDatosCadena, isActive) {

    isActive = isActive === 1 ? 0 : 1;
    var Activo = isActive ? "Activo" : "Inactivo";
    var send;
    send = {"actualizarStatusCliente": 1};
    send.opcion = 3;
    send.ID_ColeccionCadena = ID_ColeccionCadena;
    send.ID_ColeccionDeDatosCadena = ID_ColeccionDeDatosCadena;
    send.isActive = isActive;

    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        Accept: "application/json, text/javascript2",
        contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
        url: "../../mantenimiento/adminCadena/configAdminCadena.php",
        data: send,
        success: function (data) {
            if (data.str > 0) {
                alertify.success(data[0].mensaje);
                //$("'#"+ID_ColeccionCadena+"|"+ID_ColeccionDeDatosCadena+"'").find('status').val(Activo);
            }
        }
    });
}