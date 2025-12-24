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
    var origenBD = $("#inDBLocalOrigen").val();
    var destino = $('#lstLocalDestino').val();
    var destinoBD = $("#inBDLocalDestino").val();
    configurarTransferenciaVentaRestaurante(accion, idColeccionCadena, idParametroCadena, idColeccionRestaurante, idParametroRestaurante, origen, origenBD, destino, destinoBD,"Activos");
};

var validarModificarConfiguracionTransferenciaVentaRestaurante = function (origen, prm_estadoColeccion) {
    var accion = 0;
    var idColeccionCadena = $('#lstColeccionCadena :selected').val();
    var idParametroCadena = $('#lstColeccionCadena :selected').attr('parametro');
    var idColeccionRestaurante = $("#fila" + origen).attr("coleccion");
    var idParametroRestaurante = $("#fila" + origen).attr("parametro");
    var origenBD = $("#inDBLocalOrigen").val();
    var destino = $('#lstLocalDestino').val();
    var destinoBD = $("#inBDLocalDestino").val();
    $('#mdl_rdn_pdd_crgnd').show();
    configurarTransferenciaVentaRestaurante(accion, idColeccionCadena, idParametroCadena, idColeccionRestaurante, idParametroRestaurante, origen, origenBD, destino, destinoBD, prm_estadoColeccion);
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

    var estadoColeccion;
    if (prm_estadoColeccion === "Activos") {
        estadoColeccion = 0;
    } else if (prm_estadoColeccion === "Inactivos") {
        estadoColeccion = 1;
    }
    $('#mdl_rdn_pdd_crgnd').show();
    configurarTransferenciaVentaRestaurante(accion, idColeccionCadena, idParametroCadena, idColeccionRestaurante, idParametroRestaurante, origen, origenBD, destino, destinoBD, estadoColeccion);
    $('#mdl_rdn_pdd_crgnd').hide();

};


var configurarTransferenciaVentaRestaurante = function (accion, idColeccionCadena, idParametroCadena, idColeccionRestaurante, idParametroRestaurante, origen, origenBD, destino, destinoBD, prm_estadoColeccion) {
     
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


    var html = "<thead><tr><th>Origen</th><th>Base Datos Origen</th><th>Destino</th><th>Base Datos Destino</th><th>Activo</th></tr></thead>";
    send = {};
    send.metodo = "configurarTransferenciaVentaRestaurante";
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
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminTransferenciaVentaRestaurante/config_transferenciaRestaurante.php", data: send,
        success: function (datos) {


              alertify.success("Registro guardado correctamente.");
            if (datos.str > 0) {

                for (var i = 0; i < datos.str; i++) {

                    if (estadoColeccion === "Todos") {

                        html = html + "<tr id=\"fila" + datos[i]['origen'] + "\" onclick=\"seleccionarFila(" + datos[i]['origen'] + ",'" + datos[i]["IDColeccionRestaurante"] + "','" + datos[i]["IDParametroRestaurante"] + "' )\" ondblclick=\"modificarLocalTransferenciaVentaNo(" + datos[i]['origen'] + " , '" + estadoColeccion + "' )\" codigotiendaorigen=\"" + datos[i]['origenCodTienda'] + " - " + datos[i]['origenDescripcion'] + "\" idrestaurantedestino=\"" + datos[i]['destino'] + "\" coleccion=\"" + datos[i]['IDColeccionRestaurante'] + "\" parametro=\"" + datos[i]['IDParametroRestaurante'] + "\"><td>" + datos[i]['origenCodTienda'] + " - " + datos[i]['origenDescripcion'] + "</td><td>" + datos[i]['origenBD'] + "</td><td>" + datos[i]['destinoCodTienda'] + " - " + datos[i]['destinoDescripcion'] + "</td><td>" + datos[i]['destinoBD'] + "</td>";

                    } else {
                        html = html + "<tr id=\"fila" + datos[i]['origen'] + "\" onclick=\"seleccionarFila(" + datos[i]['origen'] + ",'" + datos[i]["IDColeccionRestaurante"] + "','" + datos[i]["IDParametroRestaurante"] + "' )\" ondblclick=\"modificarLocalTransferenciaVenta(" + datos[i]['origen'] + " , '" + estadoColeccion + "' )\" codigotiendaorigen=\"" + datos[i]['origenCodTienda'] + " - " + datos[i]['origenDescripcion'] + "\" idrestaurantedestino=\"" + datos[i]['destino'] + "\" coleccion=\"" + datos[i]['IDColeccionRestaurante'] + "\" parametro=\"" + datos[i]['IDParametroRestaurante'] + "\"><td>" + datos[i]['origenCodTienda'] + " - " + datos[i]['origenDescripcion'] + "</td><td>" + datos[i]['origenBD'] + "</td><td>" + datos[i]['destinoCodTienda'] + " - " + datos[i]['destinoDescripcion'] + "</td><td>" + datos[i]['destinoBD'] + "</td>";
                    }


                    if (datos[i]['estado'] === 1) {
                        html += "<td align='center'><input disabled='disabled' type='checkbox' checked='checked'></td></tr>";
                    } else if (datos[i]['estado'] === 0) {
                        html += "<td align='center'><input type='checkbox' disabled='disabled'></td></tr>";
                    }

                }
          
            } else {
                alertify.error("No existen configuraciones de Transferencia de Venta para esta cadena");
            }
            $('#tblLocalesConfiguradosTransferenciaVenta').html(html);
            $("#tblLocalesConfiguradosTransferenciaVenta").dataTable({"destroy": true});

            $("#mdlTransferenciaVentaLocal").modal("hide");

        }
    });
};

var agregarLocalTransferenciaVenta = function () {
    document.getElementById('btnCambiarEstado').style.display = 'none';

    var idColeccion = $('#lstColeccionCadena :selected').val();
    if (idColeccion != 0) {
        $('#mdl_rdn_pdd_crgnd').show();
        $("#inLocalOrigen").css("display", "none");
        $("#lstLocalOrigen").css("display", "block");
        cargarLocalesOrigenTransferenciaVentaCadenaSinConfiguracion();
        cargarLocalesDestinoTransferenciaVentaCadenaSinConfiguracion(4);
        $("#inLocalOrigen").val("");
        $("#inDBLocalOrigen").val("");
        $("#inBDLocalDestino").val("");
        $("#btnGuardar").attr("onclick", "validarAgregarConfiguracionTransferenciaVentaRestaurante()");
        $("#mdlTransferenciaVentaLocal").modal("show");
        $('#mdl_rdn_pdd_crgnd').hide();
    } else {
        alertify.error ("Seleccione una Configuración de Transferencia de Venta.");
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
    $('#mdl_rdn_pdd_crgnd').show();
    $("#inLocalOrigen").css("display", "block");
    $("#lstLocalOrigen").css("display", "none");
    cargarLocalesDestinoTransferenciaVentaCadenaSinConfiguracion(3);
    $("#inLocalOrigen").val($("#fila" + idRestauranteOrigen).attr("codigoTiendaOrigen"));
    $('#lstLocalDestino').val($("#fila" + idRestauranteOrigen).attr("idRestauranteDestino"));
    $("#inDBLocalOrigen").val($("#fila" + idRestauranteOrigen + " td:eq(1)").text());
    $("#inBDLocalDestino").val($("#fila" + idRestauranteOrigen + " td:eq(3)").text());
    $("#btnGuardar").attr("onclick", "validarModificarConfiguracionTransferenciaVentaRestaurante(" + idRestauranteOrigen + " , '" + estadoColeccion + "')");

    document.getElementById('btnCambiarEstado').style.display = 'inline';
    $("#btnCambiarEstado").attr("onclick", "fn_activarODesactivarColeccion(" + idRestauranteOrigen + " , '" + estadoColeccion + "')");

    if (estadoColeccion === "Activos") {
        $("#btnCambiarEstado").attr("class", "btn btn-danger");
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


    fn_cargaColeccionTransferenciaVentas(1);
    $('#lblOrigen').html('Origen Venta: ' + $('#cadenaOrigen').val());
    $('#lblDestino').html('Destino Venta: ' + $('#cadenaDestino').val());

    var html = "";
    html = "<thead><tr><th>Origen</th><th>Base Datos Origen</th><th>Destino</th><th>Base Datos Destino</th><th>Activo</th></tr></thead>";

    if (idColeccion != 0) {
        send = {};
        send.metodo = "cargarConfiguracionTransferenciaVentaRestaurante";
        send.idColeccion = idColeccion;
        send.idParametro = idParametro;
        send.estado = status;
        $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminTransferenciaVentaRestaurante/config_transferenciaRestaurante.php", data: send,
            success: function (datos) {


                if (datos.str > 0) {

                    for (var i = 0; i < datos.str; i++) {


                        if (estadoColeccion === "Todos") {

                            html = html + "<tr id=\"fila" + datos[i]['origen'] + "\" onclick=\"seleccionarFila(" + datos[i]['origen'] + ",'" + datos[i]["IDColeccionRestaurante"] + "','" + datos[i]["IDParametroRestaurante"] + "' )\" ondblclick=\"modificarLocalTransferenciaVentaNo(" + datos[i]['origen'] + " , '" + estadoColeccion + "' )\" codigotiendaorigen=\"" + datos[i]['origenCodTienda'] + " - " + datos[i]['origenDescripcion'] + "\" idrestaurantedestino=\"" + datos[i]['destino'] + "\" coleccion=\"" + datos[i]['IDColeccionRestaurante'] + "\" parametro=\"" + datos[i]['IDParametroRestaurante'] + "\"><td>" + datos[i]['origenCodTienda'] + " - " + datos[i]['origenDescripcion'] + "</td><td>" + datos[i]['origenBD'] + "</td><td>" + datos[i]['destinoCodTienda'] + " - " + datos[i]['destinoDescripcion'] + "</td><td>" + datos[i]['destinoBD'] + "</td>";

                        } else {
                            html = html + "<tr id=\"fila" + datos[i]['origen'] + "\" onclick=\"seleccionarFila(" + datos[i]['origen'] + ",'" + datos[i]["IDColeccionRestaurante"] + "','" + datos[i]["IDParametroRestaurante"] + "' )\" ondblclick=\"modificarLocalTransferenciaVenta(" + datos[i]['origen'] + " , '" + estadoColeccion + "' )\" codigotiendaorigen=\"" + datos[i]['origenCodTienda'] + " - " + datos[i]['origenDescripcion'] + "\" idrestaurantedestino=\"" + datos[i]['destino'] + "\" coleccion=\"" + datos[i]['IDColeccionRestaurante'] + "\" parametro=\"" + datos[i]['IDParametroRestaurante'] + "\"><td>" + datos[i]['origenCodTienda'] + " - " + datos[i]['origenDescripcion'] + "</td><td>" + datos[i]['origenBD'] + "</td><td>" + datos[i]['destinoCodTienda'] + " - " + datos[i]['destinoDescripcion'] + "</td><td>" + datos[i]['destinoBD'] + "</td>";
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



var cargarLocalesOrigenTransferenciaVentaCadenaSinConfiguracion = function () {
    var idParametro = $('#lstColeccionCadena :selected').attr('parametro');
    var idColeccion = $('#lstColeccionCadena :selected').val();
    var html = "";
    send = {};
    send.metodo = "cargarLocalesTransferenciaVentaCadenaSinConfiguracion";
    send.idColeccion = idColeccion;
    send.idParametro = idParametro;
    $.ajax({async: false, type: "POST", dataType: "json", "Accept": "application/json, text/javascript2", contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json", url: "../adminTransferenciaVentaRestaurante/config_transferenciaRestaurante.php", data: send,
        success: function (datos) {
            if (datos.str > 0) {
                // document.getElementById('btnGuardar').style.display = 'inline';
                for (var i = 0; i < datos.str; i++) {
                    html = html + "<option value='" + datos[i]['IDRestaurante'] + "'>" + datos[i]['CodRestaurante'] + " - " + datos[i]['Descripcion'] + "</option>";
                }
            } else {
                 alertify.error("No existen configuraciones de Transferencia de Venta para esta cadena o están todas configuradas");
                document.getElementById('btnGuardar').style.display = 'none';
            }
            $('#lstLocalOrigen').html(html);
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