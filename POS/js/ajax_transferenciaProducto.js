$(document).ready(function () {
    $('#mdl_rdn_pdd_crgnd').show();
    cargarConfiguracionTransferenciaVentaCadena();
    $('#mdl_rdn_pdd_crgnd').hide();
});

var validarAgregarConfiguracionTransferenciaVentaRestaurante = function () {
    var accion = 1;
    var idColeccionCadena = $('#lstColeccionCadena :selected').val();
    var idParametroCadena = $('#lstColeccionCadena :selected').attr('parametro');
    var idColeccionRestaurante = "";
    var idParametroRestaurante = "";
    var origen = $("#lstLocalOrigen").val();
    var destino = $('#lstLocalDestino').val();

    configurarTransferenciaVentaRestaurante(accion, idColeccionCadena, idParametroCadena, idColeccionRestaurante, idParametroRestaurante, origen, 'origenBD', destino, 'Activos');
};

var validarModificarConfiguracionTransferenciaVentaRestaurante = function (origen, prm_estadoColeccion) {
    var accion = 0;
    var idColeccionCadena = $('#lstColeccionCadena :selected').val();
    var idParametroCadena = $('#lstColeccionCadena :selected').attr('parametro');
    var idColeccionRestaurante = $("#fila" + origen).attr("coleccion");
    var idParametroRestaurante = $("#fila" + origen).attr("parametro");
    var origenBD = $("#inDBLocalOrigen").val();
    var destino = $('#lstLocalDestino').val();
    var olDestino = $('#oldDestino').val();
    var destinoBD = $("#inBDLocalDestino").val();
    $('#mdl_rdn_pdd_crgnd').show();
    ActualizarTransferenciaVentaRestaurante(accion, idColeccionCadena, idParametroCadena, idColeccionRestaurante, idParametroRestaurante, origen, origenBD, destino, destinoBD, prm_estadoColeccion, olDestino);
    $('#mdl_rdn_pdd_crgnd').hide();
};


var fn_activarODesactivarColeccion = function (origen, prm_estadoColeccion) {
    var accion = 0;
    var idColeccionCadena = $('#lstColeccionCadena :selected').val();
    var idParametroCadena = $('#lstColeccionCadena :selected').attr('parametro');
    var idColeccionRestaurante = $("#fila" + origen).attr("coleccion");
    var idParametroRestaurante = $("#fila" + origen).attr("parametro");
    var origenBD = $("#inDBLocalOrigen").val();
    var destino = $('#lstLocalDestino').val();
    var destinoBD = $("#inBDLocalDestino").val();
    var oldDestino = $('#oldDestino').val();
    var estadoColeccion;
    if (prm_estadoColeccion === "Activos") {
        estadoColeccion = 0;
    } else if (prm_estadoColeccion === "Inactivos") {
        estadoColeccion = 1;
    }
    $('#mdl_rdn_pdd_crgnd').show();
    ActualizarTransferenciaVentaRestaurante(accion, idColeccionCadena, idParametroCadena, idColeccionRestaurante, idParametroRestaurante, origen, origenBD, destino, destinoBD, estadoColeccion, oldDestino);
    $('#mdl_rdn_pdd_crgnd').hide();

};


var fn_eliminarPlusColeccion = function (origen, prm_estadoColeccion) {
    var accion = 2; // eliminar
    var idColeccionCadena = $('#lstColeccionCadena :selected').val();
    var idParametroCadena = $('#lstColeccionCadena :selected').attr('parametro');
    var idColeccionRestaurante = $("#fila" + origen).attr("coleccion");
    var idParametroRestaurante = $("#fila" + origen).attr("parametro");
    var origenBD = $("#inDBLocalOrigen").val();
    var destino = $('#lstLocalDestino').val();
    var destinoBD = $("#inBDLocalDestino").val();
    var oldDestino = $('#oldDestino').val();
    var estadoColeccion;
    if (prm_estadoColeccion === "Activos") {
        estadoColeccion = 0;
    } else if (prm_estadoColeccion === "Inactivos") {
        estadoColeccion = 1;
    }
    $('#mdl_rdn_pdd_crgnd').show();
    ActualizarTransferenciaVentaRestaurante(accion, idColeccionCadena, idParametroCadena, idColeccionRestaurante, idParametroRestaurante, origen, origenBD, destino, destinoBD, estadoColeccion, oldDestino);
    $('#mdl_rdn_pdd_crgnd').hide();

};


var ActualizarTransferenciaVentaRestaurante = function (accion, idColeccionCadena, idParametroCadena, idColeccionRestaurante, idParametroRestaurante, origen, origenBD, destino, destinoBD, prm_estadoColeccion, oldDestino) {

    var estadoColeccion = "", activo;
    if (prm_estadoColeccion === "Activos") {
        estadoColeccion = "Activos";
        activo = 1;
    } else if (prm_estadoColeccion === "Inactivos") {
        estadoColeccion = "Inactivos";
        activo = 1;
    } else if (prm_estadoColeccion === "Inactivos") {
        estadoColeccion = "Todos";
    } else if (prm_estadoColeccion === 1) {
        activo = 1;
    } else
    {
        activo = 0;
    }

    send = {};
    send.metodo = "ActualizarTransferenciaVentaProducto";
    send.accion = accion;
    send.idColeccionCadena = idColeccionCadena;
    send.idParametroCadena = idParametroCadena;
    send.idColeccionRestaurante = idColeccionRestaurante;
    send.idParametroRestaurante = idParametroRestaurante;
    send.origen = origen;
    send.origenBD = origenBD;
    send.destino = destino;
    send.destinoBD = destinoBD;
    send.estadoColecc = activo;
    send.oldDestino = oldDestino;

    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminTransferenciaVentaProducto/config_transferenciaProducto.php", data: send,
        success: function (datos) {
            for (var i = 0; i < datos.str; i++) {

                if (datos[i]['respuesta'] === '1') {

                    alertify.success(datos[i]['mensaje']);
                    $("#mdlTransferenciaVentaLocal").modal("hide");



                } else if (datos[i]['respuesta'] === '0') {
                    alertify.error(datos[i]['mensaje']);
                    return false;
                }
            }

            if (prm_estadoColeccion === "Activos") {
                fn_OpcionSeleccionada('Activos');
            } else if (prm_estadoColeccion === "Inactivos") {
                fn_OpcionSeleccionada('Inactivos');

            } else if (prm_estadoColeccion === "Todos") {
                fn_OpcionSeleccionada('Todos');
            } else if (prm_estadoColeccion === 1) {
                fn_OpcionSeleccionada('Inactivos');
            } else
            {
                fn_OpcionSeleccionada('Activos');
            }


            $("#mdlTransferenciaVentaLocal").modal("hide");

        }
    });
};


var configurarTransferenciaVentaRestaurante = function (accion, idColeccionCadena, idParametroCadena, idColeccionRestaurante, idParametroRestaurante, origen, origenBD, destino, destinoBD, prm_estadoColeccion) {

    var estadoColeccion = "", activo;
    if (prm_estadoColeccion === "Activos") {
        estadoColeccion = "Activos";
        activo = 1;
    } else if (prm_estadoColeccion === "Inactivos") {
        estadoColeccion = "Inactivos";
        activo = 0;
    } else if (prm_estadoColeccion === "Inactivos") {
        estadoColeccion = "Todos";
    } else if (prm_estadoColeccion === 1) {
        activo = 1;
    } else
    {
        activo = 0;
    }

    send = {};
    send.metodo = "configurarTransferenciaVentaProducto";
    send.accion = accion;
    send.idColeccionCadena = idColeccionCadena;
    send.idParametroCadena = idParametroCadena;
    send.idColeccionRestaurante = idColeccionRestaurante;
    send.idParametroRestaurante = idParametroRestaurante;
    send.origen = origen;
    send.origenBD = origenBD;
    send.destino = destino;
    send.destinoBD = destinoBD;
    send.estadoColecc = activo;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminTransferenciaVentaProducto/config_transferenciaProducto.php", data: send,
        success: function (datos) {
            for (var i = 0; i < datos.str; i++) {

                if (datos[i]['respuesta'] === '1') {

                    alertify.success(datos[i]['mensaje']);
                    $("#mdlTransferenciaVentaLocal").modal("hide");



                } else if (datos[i]['respuesta'] === '0') {
                    alertify.error(datos[i]['mensaje']);
                    return false;
                }
            }
            fn_OpcionSeleccionada('Activos');

            $("#mdlTransferenciaVentaLocal").modal("hide");

        }
    });
};

var agregarLocalTransferenciaVenta = function () {
    document.getElementById('btnCambiarEstado').style.display = 'none';
    document.getElementById('btnEliminar').style.display = 'none';
    var idColeccion = $('#lstColeccionCadena :selected').val();
    var idParametro = $('#lstColeccionCadena :selected').attr('parametro');


    if (idColeccion != 0) {
        $('#mdl_rdn_pdd_crgnd').show();
        $("#inLocalOrigen").css("display", "none");
        $("#lstLocalOrigen").css("display", "block");

        cargarProductosOrigenTransferenciaVentaCadenaSinConfiguracion(2, '');
        cargarProductosOrigenTransferenciaVentaCadenaSinConfiguracion(3, '');

        $('#lstLocalDestino').trigger('chosen:updated');
        $('#lstLocalOrigen').trigger('chosen:updated');

        $("#inLocalOrigen").val("");
        $("#inDBLocalOrigen").val("");
        $("#inBDLocalDestino").val("");
        $("#btnGuardar").attr("onclick", "validarAgregarConfiguracionTransferenciaVentaRestaurante()");
        $("#mdlTransferenciaVentaLocal").modal("show");
        $('#mdl_rdn_pdd_crgnd').hide();
        $("#lstLocalOrigen_chosen").show();
        $('#lstLocalDestino').trigger('chosen:updated');



    } else {
      alertify.error("Seleccione una Configuración de Transferencia de Venta.");
    }
};


var fn_inactivarTransferenciaVentas = function (origen, estadoColeccion) {
    var accion = 0;
    var idColeccionCadena = $('#lstColeccionCadena :selected').val();
    var idParametroCadena = $('#lstColeccionCadena :selected').attr('parametro');
    var idColeccionRestaurante = $("#fila" + origen).attr("coleccion");
    var idParametroRestaurante = $("#fila" + origen).attr("parametro");
    var origenBD = $("#inDBLocalOrigen").val();
    var destino = $('#lstLocalDestino').val();
    var destinoBD = $("#inBDLocalDestino").val();
    configurarTransferenciaVentaRestaurante(accion, idColeccionCadena, idParametroCadena, idColeccionRestaurante, idParametroRestaurante, origen, origenBD, destino, destinoBD, estadoColeccion);
};

var modificarLocalTransferenciaVenta = function (idRestauranteOrigen, estadoColeccion) {

document.getElementById('btnEliminar').style.display = 'inline';
    $('#mdl_rdn_pdd_crgnd').show();
    $("#inLocalOrigen").css("display", "block");
    $("#lstLocalOrigen").css("display", "none");

    cargarProductosOrigenTransferenciaVentaCadenaSinConfiguracion(3, $("#fila" + idRestauranteOrigen).attr("idRestauranteDestino"));
    //$('#lstLocalDestino').hide();

    $('#lstLocalDestino').trigger('liszt:updated');
    $("#inLocalOrigen").val($("#fila" + idRestauranteOrigen).attr("codigoTiendaOrigen"));
    $('#lstLocalDestino').val($("#fila" + idRestauranteOrigen).attr("idRestauranteDestino"));

    $('#lstLocalDestino_chosen').val($("#fila" + idRestauranteOrigen).attr("idRestauranteDestino"));

    // Actualizar la seleccion del chosen
    $('#lstLocalDestino').trigger('chosen:updated');

    $("#lstLocalOrigen_chosen").hide();
    $('#oldDestino').val($("#fila" + idRestauranteOrigen).attr("idRestauranteDestino"));

    $("#inDBLocalOrigen").val($("#fila" + idRestauranteOrigen + " td:eq(1)").text());
    $("#inBDLocalDestino").val($("#fila" + idRestauranteOrigen + " td:eq(3)").text());
    $("#btnGuardar").attr("onclick", "validarModificarConfiguracionTransferenciaVentaRestaurante(" + idRestauranteOrigen + " , '" + estadoColeccion + "')");

    document.getElementById('btnCambiarEstado').style.display = 'inline';
    $("#btnCambiarEstado").attr("onclick", "fn_activarODesactivarColeccion(" + idRestauranteOrigen + " , '" + estadoColeccion + "')");

    $("#btnEliminar").attr("onclick", "fn_eliminarPlusColeccion(" + idRestauranteOrigen + " , '" + estadoColeccion + "')");




   $("#btnEliminar").attr("class", "btn btn-danger");
    if (estadoColeccion === "Activos") {
        $("#btnCambiarEstado").attr("class", "btn btn-warning");
        $("#btnCambiarEstado").html("Inactivar");

    } else if (estadoColeccion === "Inactivos") {
        $("#btnCambiarEstado").attr("Class", "btn btn-success");
        $("#btnCambiarEstado").html("Activar");


    }


    $("#mdlTransferenciaVentaLocal").modal("show");
    $('#mdl_rdn_pdd_crgnd').hide();
};

var cargarConfiguracionTransferenciaVentaCadena = function () {

    var html = "<option value='0'>-- Seleccion Transferencia Venta --</option>";
    send = {};
    send.metodo = "cargarConfiguracionTransferenciaVentaCadena";
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminTransferenciaVentaRestaurante/config_transferenciaRestaurante.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html = html + "<option value='" + datos[i]['coleccion'] + "' parametro='" + datos[i]['parametro'] + "' cadenaDestino='" + datos[i]['IDCadenaDestino'] + "'>" + datos[i]['descripcion'] + "</option>";
                }
            } else {
                alertify.success("No existen configuraciones de Transferencia de Venta para esta cadena"); //  alert("No existen configuraciones de Transferencia de Venta para esta cadena");
         
            }
            $('#lstColeccionCadena').html(html);
        }
    });
};

function fn_OpcionSeleccionada(ls_opcion) {
    $("#mdl_rdn_pdd_crgnd").show();

    var estadoColeccion;
    if (ls_opcion === 'Todos') {
        estadoColeccion = ls_opcion;
        cargarLocalesConfiguradosTransferenciaVentaPorCadena(estadoColeccion);
    } else if (ls_opcion === 'Activos') {
        estadoColeccion = ls_opcion;
        cargarLocalesConfiguradosTransferenciaVentaPorCadena(estadoColeccion);
    } else if (ls_opcion === 'Inactivos') {
        estadoColeccion = ls_opcion;
        cargarLocalesConfiguradosTransferenciaVentaPorCadena(estadoColeccion);
    }
    $("#mdl_rdn_pdd_crgnd").hide();


}

var cargarLocalesConfiguradosTransferenciaVentaPorCadena = function (prm_estadoColeccion) {



    var idParametro = $('#lstColeccionCadena :selected').attr('parametro');
    var idColeccion = $('#lstColeccionCadena :selected').val();
    var status = "%%";

    var estadoColeccion = "";
    if (prm_estadoColeccion === "Activos") {
        estadoColeccion = "Activos";
        status = "%1%";
    } else if (prm_estadoColeccion === "Inactivos") {
        estadoColeccion = "Inactivos";
        status = "%0%";
    } else {
        estadoColeccion = "Todos";
        status = "%%";
    }

    // cargar en hidden cadenas de origen y destino para mostrar como información dentro del modal.
    fn_cargaColeccionTransferenciaVentas(1);
    $('#lblOrigen').html('Origen: ' + $('#cadenaOrigen').val());
    $('#lblDestino').html('Destino: ' + $('#cadenaDestino').val());


    var html = "<thead><tr> <th colspan='2'  class='text-center'>ORIGEN</th>  <th colspan='2' class='text-center'>DESTINO</th> <th rowspan='2' class='text-center'>Activo</th> </tr>  ";
    html += "<tr><th>NumPlu</th><th>Descripción</th><th>NumPlu</th><th>Descripción</th></tr></thead>";

    if (idColeccion != 0) {
        send = {};
        send.metodo = "cargarConfiguracionTransferenciaVentaProducto";
        send.idColeccion = idColeccion;
        send.idParametro = idParametro;
        send.estado = status;
        $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminTransferenciaVentaProducto/config_transferenciaProducto.php", data: send,
            success: function (datos) {
                if (datos.str > 0) {
                    for (var i = 0; i < datos.str; i++) {
                        if (estadoColeccion === "Todos") {

                            html = html + "<tr id=\"fila" + datos[i]['idProductoOrigen'] + "\" onclick=\"seleccionarFila(" + datos[i]['idProductoOrigen'] + ",'" + datos[i]["idProductoOrigen"] + "','" + datos[i]["idProductoOrigen"] + "' )\" ondblclick=\"modificarLocalTransferenciaVentaNo(" + datos[i]['idProductoOrigen'] + " , '" + estadoColeccion + "' )\" codigotiendaorigen=\"" + datos[i]['numProductoOrigen'] + " - " + datos[i]['productoOrigen'] + "\" idrestaurantedestino=\"" + datos[i]['idProductoDestino'] + "\" coleccion=\"" + datos[i]['idProductoDestino'] + "\" parametro=\"" + datos[i]['idProductoDestino'] + "\"><td>" + datos[i]['numProductoOrigen'] + "</td><td>" + datos[i]['productoOrigen'] +  " (" + datos[i]['claProductoOrigen'] + ")"  + "</td><td>" + datos[i]['numProductoDestino'] + "</td><td>" + datos[i]['productoDestino']  +  " (" + datos[i]['claProductoDestino'] + ")"  + "</td>";
                        } else {
                            html = html + "<tr id=\"fila" + datos[i]['idProductoOrigen'] + "\" onclick=\"seleccionarFila(" + datos[i]['idProductoOrigen'] + ",'" + datos[i]["idProductoOrigen"] + "','" + datos[i]["idProductoOrigen"] + "' )\" ondblclick=\"modificarLocalTransferenciaVenta(" + datos[i]['idProductoOrigen'] + " , '" + estadoColeccion + "' )\" codigotiendaorigen=\"" + datos[i]['numProductoOrigen'] + " - " + datos[i]['productoOrigen'] + "\" idrestaurantedestino=\"" + datos[i]['idProductoDestino'] + "\" coleccion=\"" + datos[i]['idProductoDestino'] + "\" parametro=\"" + datos[i]['idProductoDestino'] + "\"><td>" + datos[i]['numProductoOrigen'] + "</td><td>" + datos[i]['productoOrigen']  +  " (" + datos[i]['claProductoOrigen'] + ")"  + "</td><td>" + datos[i]['numProductoDestino'] + "</td><td>" + datos[i]['productoDestino'] +  " (" + datos[i]['claProductoDestino'] + ")"  + "</td>";
                        }

                        if (datos[i]['estado'] === 1) {
                            html += "<td align='center'><input disabled='disabled' type='checkbox' checked='checked'></td></tr>";
                        } else if (datos[i]['estado'] === 0) {
                            html += "<td align='center'><input type='checkbox' disabled='disabled'></td></tr>";
                        }

                    }
                } else {
                    alertify.error("No existen configuraciones de Transferencia de Venta para esta cadena");//   alert("No existen configuraciones de Transferencia de Venta para esta cadena");
                }



                $('#tblLocalesConfiguradosTransferenciaVenta').html(html);
                $("#tblLocalesConfiguradosTransferenciaVenta").dataTable({"destroy": true});
                // $('#tblLocalesConfiguradosTransferenciaVenta').show(500);


            }
        });
    } else {
        alertify.error("Seleccione una Colección");
        $('#tblLocalesConfiguradosTransferenciaVenta').html(html);
    }
};



var cargarProductosOrigenTransferenciaVentaCadenaSinConfiguracion = function ($opcion, $DestinoIncluir) {


    var idParametro = $('#lstColeccionCadena :selected').attr('parametro');
    var idColeccion = $('#lstColeccionCadena :selected').val();

    var html = "";
    send = {};
    send.metodo = "cargarProductoCadenaOrigen";
    send.idColeccion = idColeccion;
    send.idParametro = idParametro;
    send.DestinoIncluir = $DestinoIncluir;
    send.tipo = $opcion;  // 2 origen 3 destino
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminTransferenciaVentaProducto/config_transferenciaProducto.php", data: send,
        success: function (datos) {


            if (datos.str > 0) {
                // document.getElementById('btnGuardar').style.display = 'inline';
                for (var i = 0; i < datos.str; i++) {
                    html = html + "<option value='" + datos[i]['plu_id'] + "'>" + datos[i]['plu_num_plu'] + " - " + datos[i]['plu_descripcion'] + " / " + datos[i]['cla_Nombre'] + "    </option>";
                }
            } else {
                alert("No existen configuraciones de Transferencia de Venta para esta cadena1");
                 document.getElementById('btnGuardar').style.display = 'none';
                
            }


            if ($opcion === 2) {


                $('#lstLocalOrigen').html(html);
                $('#lstLocalOrigen').hide();

                $("#lstLocalOrigen").chosen({no_results_text: "No existen registros para ",
                search_contains: true
               
                });
                $("#lstLocalOrigen_chosen").css('width', '300');



            } else if ($opcion === 3) {
                $('#lstLocalDestino').html(html);
                $("#lstLocalDestino").chosen({no_results_text: "No existen registros para ",
                 search_contains: true
                });
                $("#lstLocalDestino_chosen").css('width', '300');



            }


        }
    });
};

var cargarLocalesDestinoTransferenciaVentaCadenaSinConfiguracion = function (accion) {
    var idParametro = $('#lstColeccionCadena :selected').attr('parametro');
    var idColeccion = $('#lstColeccionCadena :selected').val();
    var IDCadenaDestino = $('#lstColeccionCadena :selected').attr('cadenaDestino');
    var html = "";
    send = {};
    send.metodo = "cargarLocalesDestinoTransferenciaVentaCadenaSinConfiguracion";
    send.accion = accion;
    send.idColeccion = idColeccion;
    send.idParametro = idParametro;
    send.idCadenaDestino = IDCadenaDestino;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminTransferenciaVentaRestaurante/config_transferenciaRestaurante.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    html = html + "<option value='" + datos[i]['IDRestaurante'] + "'>" + datos[i]['CodRestaurante'] + " - " + datos[i]['Descripcion'] + "</option>";
                }
            } else {
                alert("No existen configuraciones de Transferencia de Venta para esta cadena");
            }
            $('#lstLocalDestino').html(html);
        }
    });
};

var seleccionarFila = function (idRestauranteOrigen, ID_ColeccionCadena, ID_ColeccionDeDatosCadena) {
    $("#txt_hidden_ID_ColeccionCadena").val(ID_ColeccionCadena);
    $("#txt_hidden_ID_ColeccionDeDatosCadena").val(ID_ColeccionDeDatosCadena);
    //document.getElementById('btnInactivar').disabled=false ;
    $('#tblLocalesConfiguradosTransferenciaVenta tr').removeClass("success");
    $("#fila" + idRestauranteOrigen).addClass("success");
};

function fn_cargaColeccionTransferenciaVentas(accion)

{
    var idColeccion = $('#lstColeccionCadena :selected').val();
    var idParametro = $('#lstColeccionCadena :selected').attr('parametro');

    send = {};
    send.metodo = "cargarorigendestino";
    send.accion = accion;
    send.coleccioncadena = idColeccion;
    send.colecciondedatoscadena = idParametro;
    send.estadoColecc = 1;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminTransferenciaVentaProducto/config_transferenciaProducto.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++) {
                    $('#cadenaOrigen').val(datos[i]['origen']);
                    $('#cadenaDestino').val(datos[i]['destino']);
                }
            } else {
            }
        }
    });
}
;
